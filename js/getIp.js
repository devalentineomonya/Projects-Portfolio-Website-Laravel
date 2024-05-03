
    function downloadUserInfo(ip) {
        return $.ajax({
            url: "https://ipapi.co/" + ip + "/json/",
            method: "GET",
            crossDomain: true,
            dataType: "json"
        })
            .then(function (response) {
                return {
                    country_name: response.country_name,
                    city: response.city,
                    latitude: response.latitude,
                    longitude: response.longitude,
                    organization: response.org,
                    region: response.region
                };
            })
            .catch(function (jqXHR, textStatus, errorThrown) {
                throw new Error("Error: " + textStatus + "\n" + errorThrown);
            });
    }


    var BrowserDetect = {
        init: function () {
            this.browser = this.searchString(this.dataBrowser) || "An unknown browser";
            this.version = this.searchVersion(navigator.userAgent)
                || this.searchVersion(navigator.appVersion)
                || "UD Vers";
            this.OS = this.searchString(this.dataOS) || "UD OS";
        },
        searchString: function (data) {
            for (var i = 0; i < data.length; i++) {
                var dataString = data[i].string;
                var dataProp = data[i].prop;
                this.versionSearchString = data[i].versionSearch || data[i].identity;
                if (dataString) {
                    if (dataString.indexOf(data[i].subString) != -1)
                        return data[i].identity;
                } else if (dataProp)
                    return data[i].identity;
            }
        },
        searchVersion: function (dataString) {
            var index = dataString.indexOf(this.versionSearchString);
            if (index == -1) return;
            return parseFloat(dataString.substring(index + this.versionSearchString.length + 1));
        },
        dataBrowser: [
            {
                string: navigator.userAgent,
                subString: "Edg",
                identity: "Edge"
            },
            {
                string: navigator.userAgent,
                subString: "Chrome",
                identity: "Chrome"
            },
            {
                string: navigator.userAgent,
                subString: "OmniWeb",
                versionSearch: "OmniWeb/",
                identity: "OmniWeb"
            },
            {
                string: navigator.vendor,
                subString: "Apple",
                identity: "Safari",
                versionSearch: "Version"
            },
            {
                prop: window.opera,
                identity: "Opera",
                versionSearch: "Version"
            },
            {
                string: navigator.vendor,
                subString: "iCab",
                identity: "iCab"
            },
            {
                string: navigator.vendor,
                subString: "KDE",
                identity: "Konqueror"
            },
            {
                string: navigator.userAgent,
                subString: "Firefox",
                identity: "Firefox"
            },
            {
                string: navigator.vendor,
                subString: "Camino",
                identity: "Camino"
            },
            {
                string: navigator.userAgent,
                subString: "Netscape",
                identity: "Netscape"
            },
            {
                string: navigator.userAgent,
                subString: "MSIE",
                identity: "Explorer",
                versionSearch: "MSIE"
            },
            {
                string: navigator.userAgent,
                subString: "Gecko",
                identity: "Mozilla",
                versionSearch: "rv"
            },
            {
                string: navigator.userAgent,
                subString: "Mozilla",
                identity: "Netscape",
                versionSearch: "Mozilla"
            }
        ],
        dataOS: [
            {
                string: navigator.platform,
                subString: "Win",
                identity: "Windows"
            },
            {
                string: navigator.platform,
                subString: "Mac",
                identity: "Mac"
            },
            {
                string: navigator.userAgent,
                subString: "iPhone",
                identity: "iPhone/iPod"
            },
            {
                string: navigator.platform,
                subString: "Linux",
                identity: "Linux"
            }
        ]
    };
    


    function get_device_type() {
        const ua = navigator.userAgent;
        if (/tablet|ipad|playbook|silk/i.test(ua)) {
            return 'tablet';
        } else if (/mobile|iphone|ipod|android|blackberry|iemobile|kindle/i.test(ua)) {
            return 'mobile';
        } else {
            return 'desktop';
        }
    }


    function getUserInfo() {
        BrowserDetect.init();
        fetch('https://api.ipify.org?format=json')
            .then(response => response.json())
            .then(data => {
                const ipAddress = data.ip;
                const browserType = BrowserDetect.browser +" Version:"  + BrowserDetect.version + " Os:" + BrowserDetect.OS;
                const deviceType = get_device_type();
                downloadUserInfo(ipAddress)
                    .then(function (userInfo) {
                        // Set values in the form fields
                        document.getElementById('ipAddress').value = ipAddress;
                        document.getElementById('browser').value = browserType;
                        document.getElementById('device').value = deviceType;
                        document.getElementById('city').value = userInfo.city;
                        document.getElementById('region').value = userInfo.region;
                        document.getElementById('country').value = userInfo.country_name;
                        document.getElementById('latitude').value = userInfo.latitude;
                        document.getElementById('longitude').value = userInfo.longitude;
                        document.getElementById('organization').value = userInfo.organization;
                    })
                    .catch(function (error) {
                        console.error(error.message);
                    });
            })
            .catch(error => {
                console.error("Error getting IP: ", error);
            });
    }

    // Call getUserInfo when the page loads
    getUserInfo();
