(function() {
if ((window.location.href.indexOf("editprofile.php") > -1) || (window.location.href.indexOf("register.php") > -1)){
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

    xoops_smallworld.fn.OsmLiveSearchNow = function() {
		
        searchSel = this;
        searchSel.addClass('osm-location-picker-now');
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
        var apiUrl = 'https://photon.komoot.io/api/?q=' + value + '&limit=1'; // using this instead of nominatim.org api
        if (value.trim().length === 0) {
            var resultSel = xoops_smallworld('.osm-location-picker-result-now');
            resultSel.remove();
        } else {
            if (dataFetchXhr) {
                /* Abort if there is any previous request for new request */
                dataFetchXhr.abort();
            }
            dataFetchXhr = xoops_smallworld.ajax({
                url: apiUrl,
                type: 'GET',
				cache: true,
                contentType: 'text/plain',
                complete: function() {
                    dataFetchXhr = null;
                },
                success: function(resp) {					
                    removeResult();
                    var value = searchSel.val();
                    if (value.trim().length > 0 && resp.features.length > 0) { // goddam json arrays!! 8-/
                        dataarray = resp;
                        handleDataResp(resp.features, value);
                    }
                }
            });
        }
    });

    xoops_smallworld(document).on('click', '.map-list-item-now', function() {
        var text = xoops_smallworld(this).data('text');
        searchSel.val(text);
        removeResult();
        cityname = text;
		//xoops_smallworld('#address').attr('value', dataarray.features[0].properties.name); // using attr instead of trigger() since value not changing
		xoops_smallworld('#presentcity').attr('value', dataarray.features[0].properties.city); // using attr instead of trigger() since value not changing
		xoops_smallworld('input[name="present_lat"]').val(dataarray.features[0].geometry.coordinates[1]).trigger('change');
		xoops_smallworld('input[name="present_lng"]').val(dataarray.features[0].geometry.coordinates[0]).trigger('change');
		xoops_smallworld('input[name="present_country"]').val(dataarray.features[0].properties.country).trigger('change');
    });

    function removeResult() {
        var resultSel = xoops_smallworld('.osm-location-picker-result-now');
        resultSel.remove();
    }

    function handleDataResp(data, searchText) {		
        lastQuery = searchText;
        lastQueryResult = data;
        var listHtml = '';
        var iconHtml = "";
        if (data && data.length > 0) {
            data.forEach(function(d) {
				console.log(d.properties.name);
                var finalText = formatResultText(d.properties.name + ", " + d.properties.city + ", " + d.properties.country, searchText);
                if (finalText) {
                    //var iconHtml = '?&nbsp;';
                    iconHtml = (d.icon != undefined) ? "<img src='" + d.icon + "'/> " : " ";
                    lat += d.geometry.coordinates[1];
                    lon += d.geometry.coordinates[0];
                    listHtml +=
                        '<li class="map-list-item-now" data-text="' +
                        d.properties.name + 
                        '">' +
                        iconHtml +
                        finalText +
                        '</li>';
                }
            });
        }
        var finalHtml = '<ul class="map-list-now">' + listHtml + '</ul>';
        searchSel.after('<div class="osm-location-picker-result-now"></div>');
        var resultSel = xoops_smallworld('.osm-location-picker-result-now');
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
            finalTextArr.push('<span class="result-text-now">' + boldText + '</span>');
        });
        if (finalTextArr.length === 0) {
            return null;
        }
        return finalTextArr.join(', ');
    }

    function doMap(lat, lon, cityname_birth) {
        var map = L.map('mapid').setView([lat, lon], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        var marker = L.marker([lat, lon]).addTo(map);
        var popup = marker.bindPopup(cityname_birth);
    }
}
})();