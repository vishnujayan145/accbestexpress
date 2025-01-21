function hexToRgb(hexCode) {
    var patt = /^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
    var matches = patt.exec(hexCode);
    var rgb = "rgb(" + parseInt(matches[1], 16) + "," + parseInt(matches[2], 16) + "," + parseInt(matches[3], 16) + ")";
    return rgb;
}

function hexToRgba(hexCode, opacity) {
    var patt = /^#([\da-fA-F]{2})([\da-fA-F]{2})([\da-fA-F]{2})$/;
    var matches = patt.exec(hexCode);
    var rgb = "rgba(" + parseInt(matches[1], 16) + "," + parseInt(matches[2], 16) + "," + parseInt(matches[3], 16) + "," + opacity + ")";
    return rgb;
}

var Helper = {
    log: (item) => {
        if (window.appDebug == true) {
            typeof item == "undefined" ? "" : console.log(item);
        }
    },
    siteUrl: (extra) => {
        extra = typeof extra == "undefined" ? "" : extra;
        return window.url + extra;
    },
    uiBlock: () => {
        $(".page-loader-wrapper").fadeIn();
    },
    uiUnBlock: () => {
        $(".page-loader-wrapper").fadeOut();
    },
    ajaxRequest: (type = 'GET', url, redirect = false) => {
        if (redirect == false) {
            Helper.uiBlock();
        }

        return new Promise((resolve, reject) => {
            if (typeof type == 'undefined') {
                toastr["error"]('Please set post type');
                if (redirect == false) {
                    Helper.uiUnBlock();
                }
                reject();
            }
            if (typeof url == 'undefined') {
                toastr["error"]('Please set url');
                if (redirect == false) {
                    Helper.uiUnBlock();
                }
                reject();
            }
            let axiosOption = {
                method: type,
                url: Helper.siteUrl(url),
                headers: {
                    'Content-Type': 'multipart/form-data'
                }
            };
            axios(axiosOption).then(resData => {
                if (redirect) {
                    location.reload();
                }
                Helper.uiUnBlock();
                resolve(resData);
            }).catch((error) => {
                toastr["error"]('Network error');
                if (redirect == false) {
                    Helper.uiUnBlock();
                }
                reject();
            });
        });
    },
    configChart: (type, labels, datasets) => {
        const allConfig = {
            type,
            data: {
                labels,
                datasets
            },
            options: {
                responsive: true,
                lineTension: 1,
                scales: {
                    yAxes: [
                        {
                            ticks: {
                                beginAtZero: true,
                                padding: 25
                            }
                        }
                    ]
                }
            }
        }
        return allConfig;
    }
};

