try {
    (() => {
        let state = {
            host: 'http://stage.metrica.fun/api/v1',
            routes: {
                create_visitor: '/visitors',
                create_visit: '/visits'
            }
        };

        class requestService {

            post = (url, data, headers = {}) => {
                return fetch(url, {
                    method: 'POST',
                    crossDomain: true,
                    headers: headers,
                    body: data
                });
            };
        }

        let fetchWrapper = new requestService();

        const isStorage = () => {
            try {
                localStorage.set('test-local-storage', 1);
                localStorage.removeItem('test-local-storage');
                return 'localStorage';
            } catch (e) {
                return 'sessionStorage';
            }
        };

        let storage = window[isStorage()];

        const getTrackingId = () => {
            let myScript = document.getElementById('metrica');
            return (myScript.src.split('tracking_id' + '=')[1] || '').split('&')[0]
        };

        const getUserDevice = () => {
            let device,
                find,
                userAgent;
            device = {};

            window.device = device;

            userAgent = window.navigator.userAgent.toLowerCase();

            device.ios = () => {
                return device.iphone() || device.ipod() || device.ipad();
            };

            device.iphone = () => {
                return !device.windows() && find('iphone');
            };

            device.ipod = () => {
                return find('ipod');
            };

            device.ipad = () => {
                return find('ipad');
            };

            device.android = () => {
                return !device.windows() && find('android');
            };

            device.androidPhone = () => {
                return device.android() && find('mobile');
            };

            device.androidTablet = () => {
                return device.android() && !find('mobile');
            };

            device.blackberry = () => {
                return find('blackberry') || find('bb10') || find('rim');
            };

            device.blackberryPhone = () => {
                return device.blackberry() && !find('tablet');
            };

            device.blackberryTablet = () => {
                return device.blackberry() && find('tablet');
            };

            device.windows = () => {
                return find('windows');
            };

            device.windowsPhone = () => {
                return device.windows() && find('phone');
            };

            device.windowsTablet = () => {
                return device.windows() && (find('touch') && !device.windowsPhone());
            };

            device.fxos = () => {
                return (find('(mobile;') || find('(tablet;')) && find('; rv:');
            };

            device.fxosPhone = () => {
                return device.fxos() && find('mobile');
            };

            device.fxosTablet = () => {
                return device.fxos() && find('tablet');
            };

            device.meego = () => {
                return find('meego');
            };

            device.cordova = () => {
                return window.cordova && location.protocol === 'file:';
            };

            device.nodeWebkit = () => {
                return typeof window.process === 'object';
            };

            device.mobile = () => {
                return device.androidPhone() ||
                    device.iphone() ||
                    device.ipod() ||
                    device.windowsPhone() ||
                    device.blackberryPhone() ||
                    device.fxosPhone() ||
                    device.meego();
            };

            device.tablet = () => {
                return device.ipad() ||
                    device.androidTablet() ||
                    device.blackberryTablet() ||
                    device.windowsTablet() ||
                    device.fxosTablet();
            };

            device.desktop = () => {
                return !device.tablet() && !device.mobile();
            };

            find = (needle) => {return userAgent.indexOf(needle) !== -1;};

            return device;
        };

        const getUserAgent = () => {
            return window.navigator.userAgent;
        };

        const setToken = (token) => {
            try {
                storage.setItem('visitor_token', token);
            } catch (err) {}
        };

        const getToken = () => {
            if(storage.getItem('visitor_token') !== null){
                return storage.getItem("visitor_token");
            }
            return null;
        };

        const getPage = () => {
            return window.location.href;
        };

        const getTitle = () => {
            return document.title;
        };

        const getLanguage = () => {
            return navigator.language || navigator.userLanguage;
        };

        const getDevice = () => {
            let device = 'unknown';
            if(getUserDevice().desktop()) {
                device = 'desktop';
            } else if(getUserDevice().tablet()) {
                device = 'tablet';
            } else if(getUserDevice().mobile()) {
                device = 'mobile';
            }
            return device;
        };

        const getResolutionWith = () => {
            return window.innerWidth;
        };

        const getResolutionHeight = () => {
            return window.innerHeight;
        };

        const getVisit = () => {
            return {
                visitor_token: getToken(),
                user_agent: getUserAgent(),
                page: getPage(),
                page_title: getTitle(),
                language: getLanguage(),
                device: getDevice(),
                resolution_width: getResolutionWith(),
                resolution_height: getResolutionHeight(),
            };
        };

        const createVisitor = (tracking_number) => {
            let url = state.host + state.routes.create_visitor;
            let headers = {
                'Content-Type': 'application/json',
                'x-website': tracking_number
            };

            return fetchWrapper.post(url, {}, headers)
                .then((response) => {
                    return response.json();

                })
                .then((result) => {
                    setToken(result.data.token);
                });
        };

        const createVisit = () => {
            let url = state.host + state.routes.create_visit;
            let headers = {
                'Content-Type': 'application/json',
                'x-visitor': 'Bearer ' + getToken()
            };
            let data = JSON.stringify(getVisit());

            return fetchWrapper.post(url, data, headers)
        };

        (() => {
            let token = getToken();

            if (token) {
                createVisit();
            } else {
                createVisitor(getTrackingId())
                    .finally(() => createVisit());
            }
        })();
    })();
} catch (err) {}
