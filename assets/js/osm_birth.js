(function() {
if ((window.location.href.indexOf("editprofile.php") > -1) || (window.location.href.indexOf("register.php") > -1)){
    var searchSel_birth;
    var dataFetchXhr_birth = null;
    var lastQueryResult_birth;
    var lastQuery_birth;
    var lat_birth;
    var lon_birth;
    var cityname_birth;
    var dataarray_birth;

    xoops_smallworld.fn.OsmLiveSearchBirth = function() {
		
        searchSel_birth = this;
        searchSel_birth.addClass('osm-location-picker-birth');
        searchSel_birth.focus(function() {
            if (lastQuery_birth && lastQueryResult_birth) {
                removeResult();
                handleDataResp(lastQueryResult_birth, lastQuery_birth);
            }
        });
        searchSel_birth.blur(function() {
            setTimeout(function() {
                removeResult();
            }, 200);
        });
    };

    xoops_smallworld(document).on('keyup', searchSel_birth, function() {
        var value = searchSel_birth.val();
        var apiUrl = 'https://photon.komoot.io/api/?q=' + value + '&limit=1'; // using this instead of nominatim.org api
        if (value.trim().length === 0) {
            var resultSel = xoops_smallworld('.osm-location-picker-result-birth');
            resultSel.remove();
        } else {
            if (dataFetchXhr_birth) {
                /* Abort if there is any previous request for new request */
                dataFetchXhr_birth.abort();
            }
            dataFetchXhr_birth = xoops_smallworld.ajax({
                url: apiUrl,
                type: 'GET',
				cache: true,
                contentType: 'text/plain',
                complete: function() {
                    dataFetchXhr_birth = null;
                },
                success: function(resp) {					
                    removeResult();
                    var value = searchSel_birth.val();
                    if (value.trim().length > 0 && resp.features.length > 0) { // goddam json arrays!! 8-/
                        dataarray_birth = resp;
                        handleDataResp(resp.features, value);
                    }
                }
            });
        }
    });

    xoops_smallworld(document).on('click', '.map-list-item-birth', function() {
        var text = xoops_smallworld(this).data('text');
        searchSel_birth.val(text);
        removeResult();
        cityname_birth = text;
		xoops_smallworld('#birthplace').attr('value', dataarray_birth.features[0].properties.name); // using attr instead of trigger() since value not changing
		xoops_smallworld('input[name="birthplace_lat"]').val(dataarray_birth.features[0].geometry.coordinates[1]).trigger('change');
		xoops_smallworld('input[name="birthplace_lng"]').val(dataarray_birth.features[0].geometry.coordinates[0]).trigger('change');
		xoops_smallworld('input[name="birthplace_country"]').val(dataarray_birth.features[0].properties.country).trigger('change');
    });

    function removeResult() {
        var resultSel = xoops_smallworld('.osm-location-picker-result-birth');
        resultSel.remove();
    }

    function handleDataResp(data, searchText) {		
        lastQuery_birth = searchText;
        lastQueryResult_birth = data;
        var listHtml = '';
        var iconHtml = "";
        if (data && data.length > 0) {
            data.forEach(function(d) {
				console.log(d.properties.name);
                var finalText = formatResultText(d.properties.name + ", " + d.properties.state + ", " + d.properties.country, searchText);
                if (finalText) {
                    //var iconHtml = '?&nbsp;';
                    iconHtml = (d.icon != undefined) ? "<img src='" + d.icon + "'/> " : " ";
                    lat_birth += d.geometry.coordinates[1];
                    lon_birth += d.geometry.coordinates[0];
                    listHtml +=
                        '<li class="map-list-item-birth" data-text="' +
                        d.properties.name + 
                        '">' +
                        iconHtml +
                        finalText +
                        '</li>';
                }
            });
        }
        var finalHtml = '<ul class="map-list-birth">' + listHtml + '</ul>';
        searchSel_birth.after('<div class="osm-location-picker-result-birth"></div>');
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

    function doMap(lat_birth, lon_birth, cityname_birth) {
        var map = L.map('mapid').setView([lat_birth, lon_birth], 13);
        L.tileLayer('http://{s}.tile.osm.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap'
        }).addTo(map);
        var marker = L.marker([lat_birth, lon_birth]).addTo(map);
        var popup = marker.bindPopup(cityname_birth);
    }
}
})();