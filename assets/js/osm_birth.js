(function() {
    var searchSel;
    var dataFetchXhr = null;
    var lastQueryResult;
    var lastQuery;
    var lat;
    var lon;
    var cityname;
    var country;
    var zip;
    var dataarray;

    xoops_smallworld.fn.OsmLiveSearchBirth = function() {
		
        searchSel = this;
        searchSel.addClass('osm-location-picker-birth');
        searchSel.focus(function() {
            if (lastQuery && lastQueryResult) {
                removeResult();
                handleDataResp(lastQueryResult, lastQuery);
            }
        });
        searchSel.blur(function() {
            setTimeout(function() {
                removeResult();
            }, 200);
        });
    };

    

    xoops_smallworld(document).on('keyup', searchSel, function() {
        var value = searchSel.val();
        var apiUrl =
            'https://nominatim.openstreetmap.org/search?q=' + value + '&format=json&addressdetails=1&extratags=1&namedetails=1';
        if (value.trim().length === 0) {
            var resultSel = xoops_smallworld('.osm-location-picker-result-birth');
            resultSel.remove();
        } else {
            if (dataFetchXhr) {
                /* Abort if there is any previous request for new request */
                dataFetchXhr.abort();
            }
            dataFetchXhr = xoops_smallworld.ajax({
                url: apiUrl,
                type: 'get',
                contentType: 'application/json',
                complete: function() {
                    dataFetchXhr = null;
                },
                success: function(resp) {
                    removeResult();
                    var value = searchSel.val();
                    if (value.trim().length > 0 && resp.length > 0) {
                        dataarray = resp;
                        handleDataResp(resp, value);
                    }
                }
            });
        }
    });

    xoops_smallworld(document).on('click', '.map-list-item-birth', function() {
        var text = xoops_smallworld(this).data('text');
        searchSel.val(text);
        removeResult();
        cityname = text;
        lat = getlat(dataarray, cityname);
        lon = getlon(dataarray, cityname);

        country = getCountry(dataarray, cityname);
        zip = getZip(dataarray, cityname);
        //doMap(lat, lon, cityname);
        //xoops_smallworld('input[name="birthplace_lat"]').val("hej");

        //alert (lat + " " + lon + country + " " + zip);
    });

    function getlat(da, cn) {
        xoops_smallworld.map(da, function(value, key) {
            if (value.display_name == cn) {
                //alert(value.lat);
                xoops_smallworld('input[name="birthplace_lat"]').val(value.lat);
                return value.lat;
            }
        })
    }

    function getlon(da, cn) {
        xoops_smallworld.map(da, function(value, key) {
            if (value.display_name == cn) {
                //alert(value.lon);
                xoops_smallworld('input[name="birthplace_lng"]').val(value.lon);
                return value.lon;
            }
        })
    }

    function getCountry(da, cn) {
        xoops_smallworld.map(da, function(value, key) {
            if (value.display_name == cn) {
                //alert(value.lon);
                xoops_smallworld('input[name="birthplace_country"]').val(value.address.country_code);
                return value.address.country_code;
            }
        })
    }

    function getZip(da, cn) {
        xoops_smallworld.map(da, function(value, key) {
            if (value.display_name == cn) {
                //alert(value.lon);
                return value.address.postcode;
            }
        })
    }

    function removeResult() {
        var resultSel = xoops_smallworld('.osm-location-picker-result-birth');
        resultSel.remove();
    }

    function handleDataResp(data, searchText) {
        lastQuery = searchText;
        lastQueryResult = data;
        var listHtml = '';
        var iconHtml = "";
        if (data && data.length > 0) {
            data.forEach(function(d) {
                var finalText = formatResultText(d.display_name, searchText);
                if (finalText) {
                    //var iconHtml = '⚐&nbsp;';
                    iconHtml = (d.icon != undefined) ? "<img src='" + d.icon + "'/> " : " ";
                    lat += d.lat;
                    lon += d.lon;
                    listHtml +=
                        '<li class="map-list-item-birth" data-text="' +
                        d.namedetails.name + 
                        '">' +
                        iconHtml +
                        finalText +
                        '</li>';
                }
            });
        }
        var finalHtml = '<ul class="map-list-birth">' + listHtml + '</ul>';
        searchSel.after('<div class="osm-location-picker-result-birth"></div>');
        var resultSel = xoops_smallworld('.osm-location-picker-result-birth');
        resultSel.html(finalHtml);
    }

    function formatResultText(text, searchText) {
        var textArr = text
            .replace(/,/g, ' ')
            .split(/\s/g)
            .filter(function(t) {
                return t.trim() !== '';
            });
        var finalTextArr = [];
        textArr.forEach(function(word) {
            var wordArr = word
                .trim()
                .toLowerCase()
                .split(searchText);
            var boldText = wordArr.join('<b>' + searchText + '</b>');
            finalTextArr.push('<span class="result-text-birth">' + boldText + '</span>');
        });
        if (finalTextArr.length === 0) {
            return null;
        }
        return finalTextArr.join(', ');
    }

    function doMap(lat, lon, cityname) {
        var map = L.map('mapid').setView([lat, lon], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        var marker = L.marker([lat, lon]).addTo(map);
        var popup = marker.bindPopup(cityname);
    }

})();