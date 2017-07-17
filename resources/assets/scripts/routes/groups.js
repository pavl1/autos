export default {
    init() {
        var wp = window.wp,
            _ = window._,
            App = {
                subgroups: {},
            };

        $('.groups-link').on('click', get_subgroups);

        function get_subgroups(e) {
            let oid = JSON.parse(e.currentTarget.dataset.oid);

            if ( _.isEmpty(App.subgroups[ oid.series]) ) {
                wp.ajax.send( 'get_subgroups', {
                    data: { oid: oid },
                } ).then( (response) => {
                    App.subgroups[ response.oid.group ] = response;
                    get_subgroups_success(response, e.currentTarget.href);
                } );
            } else {
                get_subgroups_success(App.subgroups[ oid.group ], e.currentTarget.href);
            }
        }
        function get_subgroups_success(response, url) {
            let target = document.querySelector( url.substring( url.indexOf('#') ) ),
                template = wp.template( 'subgroups' );

            target.innerHTML = template({
                oid: response.oid,
                url: response.car.url,
                subgroups: response.car.subgroups,
            });
        }
    },
}
