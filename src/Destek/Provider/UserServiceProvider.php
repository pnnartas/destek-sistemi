<?php

/*
 * Bu dosya silex frameworkun SecurityServiceProvider dosyasıdır.
 *
 */

namespace Destek\Provider;

use Silex\Application;
use Silex\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\RequestMatcher;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\SecurityContextInterface;
use Symfony\Component\Security\Core\User\UserChecker;
use Symfony\Component\Security\Core\User\InMemoryUserProvider;
use Symfony\Component\Security\Core\Encoder\EncoderFactory;
use Symfony\Component\Security\Core\Encoder\MessageDigestPasswordEncoder;
use Symfony\Component\Security\Core\Authentication\Provider\DaoAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\Provider\AnonymousAuthenticationProvider;
use Symfony\Component\Security\Core\Authentication\AuthenticationProviderManager;
use Symfony\Component\Security\Core\Authentication\AuthenticationTrustResolver;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationFailureHandler;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorage;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;
use Symfony\Component\Security\Core\Authorization\Voter\RoleHierarchyVoter;
use Symfony\Component\Security\Core\Authorization\Voter\AuthenticatedVoter;
use Symfony\Component\Security\Core\Authorization\AccessDecisionManager;
use Symfony\Component\Security\Core\Role\RoleHierarchy;
use Symfony\Component\Security\Core\Validator\Constraints\UserPasswordValidator;
use Symfony\Component\Security\Http\Firewall;
use Symfony\Component\Security\Http\FirewallMap;
use Symfony\Component\Security\Http\Firewall\AbstractAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\AccessListener;
use Symfony\Component\Security\Http\Firewall\BasicAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\LogoutListener;
use Symfony\Component\Security\Http\Firewall\SwitchUserListener;
use Symfony\Component\Security\Http\Firewall\AnonymousAuthenticationListener;
use Symfony\Component\Security\Http\Firewall\ContextListener;
use Symfony\Component\Security\Http\Firewall\ExceptionListener;
use Symfony\Component\Security\Http\Firewall\ChannelListener;
use Symfony\Component\Security\Http\EntryPoint\FormAuthenticationEntryPoint;
use Symfony\Component\Security\Http\EntryPoint\BasicAuthenticationEntryPoint;
use Symfony\Component\Security\Http\EntryPoint\RetryAuthenticationEntryPoint;
use Symfony\Component\Security\Http\Session\SessionAuthenticationStrategy;
use Symfony\Component\Security\Http\Logout\SessionLogoutHandler;
use Symfony\Component\Security\Http\Logout\DefaultLogoutSuccessHandler;
use Symfony\Component\Security\Http\AccessMap;
use Symfony\Component\Security\Http\HttpUtils;

/**
 *

 */
class UserServiceProvider implements ServiceProviderInterface
{
    protected $fakeRoutes;

    public function register(Application $application)
    {
        // used to register routes for login_check and logout
        $this->fakeRoutes = array();

        $that = $this;

        $application['security.role_hierarchy'] = array();
        $application['security.access_rules'] = array();
        $application['security.hide_user_not_found'] = true;

        $r = new \ReflectionMethod('Symfony\Component\Security\Http\Firewall\ContextListener', '__construct');
        $params = $r->getParameters();
        if ('Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface' === $params[0]->getClass()->getName()) {
            $application['security.authorization_checker'] = $application->share(function ($application) {
                return new AuthorizationChecker($application['security.token_storage'], $application['security.authentication_manager'], $application['security.access_manager']);
            });

            $application['security.token_storage'] = $application->share(function ($application) {
                return new TokenStorage();
            });

            $application['security'] = $application->share(function ($application) {
                // Deprecated, to be removed in 2.0
                return new SecurityContext($application['security.token_storage'], $application['security.authorization_checker']);
            });
        } else {
            $application['security.token_storage'] = $application['security.authorization_checker'] = $application->share(function ($application) {
                return $application['security'];
            });

            $application['security'] = $application->share(function ($application) {
                // Deprecated, to be removed in 2.0
                return new SecurityContext($application['security.authentication_manager'], $application['security.access_manager']);
            });
        }

        $application['user'] = function ($application) {
            if (null === $token = $application['security.token_storage']->getToken()) {
                return;
            }

            if (!is_object($user = $token->getUser())) {
                return;
            }

            return $user;
        };


        $manualAuthenticationProviders = $application['security.authentication_providers'];

        $application['security.authentication_manager'] = $application->share(function ($application) use ($manualAuthenticationProviders) {

            $manager = new AuthenticationProviderManager($manualAuthenticationProviders);
            $manager->setEventDispatcher($application['dispatcher']);

            return $manager;
        });

        // by default, all users use the digest encoder
        $application['security.encoder_factory'] = $application->share(function ($application) {
            return new EncoderFactory(array(
                'Symfony\Component\Security\Core\User\UserInterface' => $application['security.encoder.digest'],
            ));
        });

        $application['security.encoder.digest'] = $application->share(function ($application) {
            return new MessageDigestPasswordEncoder();
        });

        $application['security.user_checker'] = $application->share(function ($application) {
            return new UserChecker();
        });

        $application['security.access_manager'] = $application->share(function ($application) {
            return new AccessDecisionManager($application['security.voters']);
        });

        $application['security.voters'] = $application->share(function ($application) {
            return array(
                new RoleHierarchyVoter(new RoleHierarchy($application['security.role_hierarchy'])),
                new AuthenticatedVoter($application['security.trust_resolver']),
            );
        });

        $application['security.firewall'] = $application->share(function ($application) {
            return new Firewall($application['security.firewall_map'], $application['dispatcher']);
        });

        $application['security.channel_listener'] = $application->share(function ($application) {
            return new ChannelListener(
                $application['security.access_map'],
                new RetryAuthenticationEntryPoint($application['request.http_port'], $application['request.https_port']),
                $application['logger']
            );
        });

        // generate the build-in authentication factories
        foreach (array('logout', 'pre_auth', 'form', 'http', 'remember_me', 'anonymous') as $type) {
            $entryPoint = null;
            if ('http' === $type) {
                $entryPoint = 'http';
            } elseif ('form' === $type) {
                $entryPoint = 'form';
            }

            $application['security.authentication_listener.factory.'.$type] = $application->protect(function ($name, $options) use ($type, $application, $entryPoint) {
                if ($entryPoint && !isset($application['security.entry_point.'.$name.'.'.$entryPoint])) {
                    $application['security.entry_point.'.$name.'.'.$entryPoint] = $application['security.entry_point.'.$entryPoint.'._proto']($name, $options);
                }

                if (!isset($application['security.authentication_listener.'.$name.'.'.$type])) {
                    $application['security.authentication_listener.'.$name.'.'.$type] = $application['security.authentication_listener.'.$type.'._proto']($name, $options);
                }

                $provider = 'anonymous' === $type ? 'anonymous' : 'dao';
                if (!isset($application['security.authentication_provider.'.$name.'.'.$provider])) {
                    $application['security.authentication_provider.'.$name.'.'.$provider] = $application['security.authentication_provider.'.$provider.'._proto']($name);
                }

                return array(
                    'security.authentication_provider.'.$name.'.'.$provider,
                    'security.authentication_listener.'.$name.'.'.$type,
                    $entryPoint ? 'security.entry_point.'.$name.'.'.$entryPoint : null,
                    $type,
                );
            });
        }

        $application['security.firewall_map'] = $application->share(function ($application) {
            $positions = array('logout', 'pre_auth', 'form', 'http', 'remember_me', 'anonymous');
            $providers = array();
            $configs = array();
            foreach ($application['security.firewalls'] as $name => $firewall) {
                $entryPoint = null;
                $pattern = isset($firewall['pattern']) ? $firewall['pattern'] : null;
                $users = isset($firewall['users']) ? $firewall['users'] : array();
                $security = isset($firewall['security']) ? (bool) $firewall['security'] : true;
                $stateless = isset($firewall['stateless']) ? (bool) $firewall['stateless'] : false;
                $context = isset($firewall['context']) ? $firewall['context'] : $name;
                unset($firewall['pattern'], $firewall['users'], $firewall['security'], $firewall['stateless'], $firewall['context']);

                $protected = false === $security ? false : count($firewall);

                $listeners = array('security.channel_listener');

                if ($protected) {
                    if (!isset($application['security.context_listener.'.$name])) {
                        if (!isset($application['security.user_provider.'.$name])) {
                            $application['security.user_provider.'.$name] = is_array($users) ? $application['security.user_provider.inmemory._proto']($users) : $users;
                        }
                        $application['security.context_listener.'.$name] = $application['security.context_listener._proto']($name, array($application['security.user_provider.'.$name]));
                    }

                    if (false === $stateless) {
                        $listeners[] = 'security.context_listener.'.$context;
                    }

                    $factories = array();
                    foreach ($positions as $position) {
                        $factories[$position] = array();
                    }

                    foreach ($firewall as $type => $options) {
                        if ('switch_user' === $type) {
                            continue;
                        }

                        // normalize options
                        if (!is_array($options)) {
                            if (!$options) {
                                continue;
                            }

                            $options = array();
                        }

                        if (!isset($application['security.authentication_listener.factory.'.$type])) {
                            throw new \LogicException(sprintf('The "%s" authentication entry is not registered.', $type));
                        }

                        $options['stateless'] = $stateless;

                        list($providerId, $listenerId, $entryPointId, $position) = $application['security.authentication_listener.factory.'.$type]($name, $options);

                        if (null !== $entryPointId) {
                            $entryPoint = $entryPointId;
                        }

                        $factories[$position][] = $listenerId;
                        $providers[] = $providerId;
                    }

                    foreach ($positions as $position) {
                        foreach ($factories[$position] as $listener) {
                            $listeners[] = $listener;
                        }
                    }

                    $listeners[] = 'security.access_listener';

                    if (isset($firewall['switch_user'])) {
                        $application['security.switch_user.'.$name] = $application['security.authentication_listener.switch_user._proto']($name, $firewall['switch_user']);

                        $listeners[] = 'security.switch_user.'.$name;
                    }

                    if (!isset($application['security.exception_listener.'.$name])) {
                        if (null == $entryPoint) {
                            $application[$entryPoint = 'security.entry_point.'.$name.'.form'] = $application['security.entry_point.form._proto']($name, array());
                        }
                        $application['security.exception_listener.'.$name] = $application['security.exception_listener._proto']($entryPoint, $name);
                    }
                }

                $configs[$name] = array($pattern, $listeners, $protected);
            }

            $application['security.authentication_providers'] = array_map(function ($provider) use ($application) {
                return $application[$provider];
            }, array_unique($providers));

            $map = new FirewallMap();
            foreach ($configs as $name => $config) {
                $map->add(
                    is_string($config[0]) ? new RequestMatcher($config[0]) : $config[0],
                    array_map(function ($listenerId) use ($application, $name) {
                        $listener = $application[$listenerId];

                        if (isset($application['security.remember_me.service.'.$name])) {
                            if ($listener instanceof AbstractAuthenticationListener) {
                                $listener->setRememberMeServices($application['security.remember_me.service.'.$name]);
                            }
                            if ($listener instanceof LogoutListener) {
                                $listener->addHandler($application['security.remember_me.service.'.$name]);
                            }
                        }

                        return $listener;
                    }, $config[1]),
                    $config[2] ? $application['security.exception_listener.'.$name] : null
                );
            }

            return $map;
        });

        $application['security.access_listener'] = $application->share(function ($application) {
            return new AccessListener(
                $application['security.token_storage'],
                $application['security.access_manager'],
                $application['security.access_map'],
                $application['security.authentication_manager'],
                $application['logger']
            );
        });

        $application['security.access_map'] = $application->share(function ($application) {
            $map = new AccessMap();

            foreach ($application['security.access_rules'] as $rule) {
                if (is_string($rule[0])) {
                    $rule[0] = new RequestMatcher($rule[0]);
                }

                $map->add($rule[0], (array) $rule[1], isset($rule[2]) ? $rule[2] : null);
            }

            return $map;
        });

        $application['security.trust_resolver'] = $application->share(function ($application) {
            return new AuthenticationTrustResolver('Symfony\Component\Security\Core\Authentication\Token\AnonymousToken', 'Symfony\Component\Security\Core\Authentication\Token\RememberMeToken');
        });

        $application['security.session_strategy'] = $application->share(function ($application) {
            return new SessionAuthenticationStrategy(SessionAuthenticationStrategy::MIGRATE);
        });

        $application['security.http_utils'] = $application->share(function ($application) {
            return new HttpUtils(isset($application['url_generator']) ? $application['url_generator'] : null, $application['url_matcher']);
        });

        $application['security.last_error'] = $application->protect(function (Request $request) {
            if (class_exists('Symfony\Component\Security\Core\Security')) {
                $error = Security::AUTHENTICATION_ERROR;
            } else {
                $error = SecurityContextInterface::AUTHENTICATION_ERROR;
            }
            if ($request->attributes->has($error)) {
                return $request->attributes->get($error)->getMessage();
            }

            $session = $request->getSession();
            if ($session && $session->has($error)) {
                $error = $session->get($error)->getMessage();
                $session->remove($error);

                return $error;
            }
        });

        // prototypes (used by the Firewall Map)

        $application['security.context_listener._proto'] = $application->protect(function ($providerKey, $userProviders) use ($application) {
            return $application->share(function () use ($application, $userProviders, $providerKey) {
                return new ContextListener(
                    $application['security.token_storage'],
                    $userProviders,
                    $providerKey,
                    $application['logger'],
                    $application['dispatcher']
                );
            });
        });

        $application['security.user_provider.inmemory._proto'] = $application->protect(function ($params) use ($application) {
            return $application->share(function () use ($application, $params) {
                $users = array();
                foreach ($params as $name => $user) {
                    $users[$name] = array('roles' => (array) $user[0], 'password' => $user[1]);
                }

                return new InMemoryUserProvider($users);
            });
        });

        $application['security.exception_listener._proto'] = $application->protect(function ($entryPoint, $name) use ($application) {
            return $application->share(function () use ($application, $entryPoint, $name) {
                return new ExceptionListener(
                    $application['security.token_storage'],
                    $application['security.trust_resolver'],
                    $application['security.http_utils'],
                    $name,
                    $application[$entryPoint],
                    null, // errorPage
                    null, // AccessDeniedHandlerInterface
                    $application['logger']
                );
            });
        });

        $application['security.authentication.success_handler._proto'] = $application->protect(function ($name, $options) use ($application) {
            return $application->share(function () use ($name, $options, $application) {
                $handler = new DefaultAuthenticationSuccessHandler(
                    $application['security.http_utils'],
                    $options
                );
                $handler->setProviderKey($name);

                return $handler;
            });
        });

        $application['security.authentication.failure_handler._proto'] = $application->protect(function ($name, $options) use ($application) {
            return $application->share(function () use ($name, $options, $application) {
                return new DefaultAuthenticationFailureHandler(
                    $application,
                    $application['security.http_utils'],
                    $options,
                    $application['logger']
                );
            });
        });

        $application['security.authentication_listener.form._proto'] = $application->protect(function ($name, $options) use ($application, $that) {
            return $application->share(function () use ($application, $name, $options, $that) {
                $that->addFakeRoute(
                    'match',
                    $tmp = isset($options['check_path']) ? $options['check_path'] : '/login_check',
                    str_replace('/', '_', ltrim($tmp, '/'))
                );

                $class = isset($options['listener_class']) ? $options['listener_class'] : 'Symfony\\Component\\Security\\Http\\Firewall\\UsernamePasswordFormAuthenticationListener';

                if (!isset($application['security.authentication.success_handler.'.$name])) {
                    $application['security.authentication.success_handler.'.$name] = $application['security.authentication.success_handler._proto']($name, $options);
                }

                if (!isset($application['security.authentication.failure_handler.'.$name])) {
                    $application['security.authentication.failure_handler.'.$name] = $application['security.authentication.failure_handler._proto']($name, $options);
                }

                return new $class(
                    $application['security.token_storage'],
                    $application['security.authentication_manager'],
                    isset($application['security.session_strategy.'.$name]) ? $application['security.session_strategy.'.$name] : $application['security.session_strategy'],
                    $application['security.http_utils'],
                    $name,
                    $application['security.authentication.success_handler.'.$name],
                    $application['security.authentication.failure_handler.'.$name],
                    $options,
                    $application['logger'],
                    $application['dispatcher'],
                    isset($options['with_csrf']) && $options['with_csrf'] && isset($application['form.csrf_provider']) ? $application['form.csrf_provider'] : null
                );
            });
        });

        $application['security.authentication_listener.http._proto'] = $application->protect(function ($providerKey, $options) use ($application) {
            return $application->share(function () use ($application, $providerKey, $options) {
                return new BasicAuthenticationListener(
                    $application['security.token_storage'],
                    $application['security.authentication_manager'],
                    $providerKey,
                    $application['security.entry_point.'.$providerKey.'.http'],
                    $application['logger']
                );
            });
        });

        $application['security.authentication_listener.anonymous._proto'] = $application->protect(function ($providerKey, $options) use ($application) {
            return $application->share(function () use ($application, $providerKey, $options) {
                return new AnonymousAuthenticationListener(
                    $application['security.token_storage'],
                    $providerKey,
                    $application['logger']
                );
            });
        });

        $application['security.authentication.logout_handler._proto'] = $application->protect(function ($name, $options) use ($application) {
            return $application->share(function () use ($name, $options, $application) {
                return new DefaultLogoutSuccessHandler(
                    $application['security.http_utils'],
                    isset($options['target_url']) ? $options['target_url'] : '/'
                );
            });
        });

        $application['security.authentication_listener.logout._proto'] = $application->protect(function ($name, $options) use ($application, $that) {
            return $application->share(function () use ($application, $name, $options, $that) {
                $that->addFakeRoute(
                    'get',
                    $tmp = isset($options['logout_path']) ? $options['logout_path'] : '/logout',
                    str_replace('/', '_', ltrim($tmp, '/'))
                );

                if (!isset($application['security.authentication.logout_handler.'.$name])) {
                    $application['security.authentication.logout_handler.'.$name] = $application['security.authentication.logout_handler._proto']($name, $options);
                }

                $listener = new LogoutListener(
                    $application['security.token_storage'],
                    $application['security.http_utils'],
                    $application['security.authentication.logout_handler.'.$name],
                    $options,
                    isset($options['with_csrf']) && $options['with_csrf'] && isset($application['form.csrf_provider']) ? $application['form.csrf_provider'] : null
                );

                $invalidateSession = isset($options['invalidate_session']) ? $options['invalidate_session'] : true;
                if (true === $invalidateSession && false === $options['stateless']) {
                    $listener->addHandler(new SessionLogoutHandler());
                }

                return $listener;
            });
        });

        $application['security.authentication_listener.switch_user._proto'] = $application->protect(function ($name, $options) use ($application, $that) {
            return $application->share(function () use ($application, $name, $options, $that) {
                return new SwitchUserListener(
                    $application['security.token_storage'],
                    $application['security.user_provider.'.$name],
                    $application['security.user_checker'],
                    $name,
                    $application['security.access_manager'],
                    $application['logger'],
                    isset($options['parameter']) ? $options['parameter'] : '_switch_user',
                    isset($options['role']) ? $options['role'] : 'ROLE_ALLOWED_TO_SWITCH',
                    $application['dispatcher']
                );
            });
        });

        $application['security.entry_point.form._proto'] = $application->protect(function ($name, array $options) use ($application) {
            return $application->share(function () use ($application, $options) {
                $loginPath = isset($options['login_path']) ? $options['login_path'] : '/login';
                $useForward = isset($options['use_forward']) ? $options['use_forward'] : false;

                return new FormAuthenticationEntryPoint($application, $application['security.http_utils'], $loginPath, $useForward);
            });
        });

        $application['security.entry_point.http._proto'] = $application->protect(function ($name, array $options) use ($application) {
            return $application->share(function () use ($application, $name, $options) {
                return new BasicAuthenticationEntryPoint(isset($options['real_name']) ? $options['real_name'] : 'Secured');
            });
        });

        $application['security.authentication_provider.dao._proto'] = $application->protect(function ($name) use ($application) {
            return $application->share(function () use ($application, $name) {
                return new DaoAuthenticationProvider(
                    $application['security.user_provider.'.$name],
                    $application['security.user_checker'],
                    $name,
                    $application['security.encoder_factory'],
                    $application['security.hide_user_not_found']
                );
            });
        });

        $application['security.authentication_provider.anonymous._proto'] = $application->protect(function ($name) use ($application) {
            return $application->share(function () use ($application, $name) {
                return new AnonymousAuthenticationProvider($name);
            });
        });

        if (isset($application['validator'])) {
            $application['security.validator.user_password_validator'] = $application->share(function ($application) {
                return new UserPasswordValidator($application['security.token_storage'], $application['security.encoder_factory']);
            });

            $application['validator.validator_service_ids'] = array_merge($application['validator.validator_service_ids'], array('security.validator.user_password' => 'security.validator.user_password_validator'));
        }
    }

    public function boot(Application $application)
    {
        $application['dispatcher']->addSubscriber($application['security.firewall']);

        foreach ($this->fakeRoutes as $route) {
            list($method, $pattern, $name) = $route;

            $application->$method($pattern)->run(null)->bind($name);
        }
    }

    public function addFakeRoute($method, $pattern, $name)
    {
        $this->fakeRoutes[] = array($method, $pattern, $name);
    }
}