export default {
    init() {
        var wp = window.wp,
            _ = window._,
            App = {
                models: {},
                options: {},
                productions: {},
            };

        $('.series-link').on('click', models);
        $('.back-to-options').on('click', () => {
            console.log(App);
            options(App.options.current);
        });

        function models(e) {
            let oid = JSON.parse(e.currentTarget.dataset.oid);
            oid.catalog = 'bmw';

            if ( _.isEmpty(App.models[ oid.series]) ) {
                wp.ajax.send( 'get_models', {
                    data: { oid: oid },
                } ).then( (response) => {
                    App.models[ response.oid.series ] = response;
                    modelsSuccess(response, e.currentTarget.href);
                } );
            } else {
                modelsSuccess(App.models[ oid.series ], e.currentTarget.href);
            }
        }
        function modelsSuccess(response, url){
            let target = document.querySelector( url.substring( url.indexOf('#') ) ),
                template = wp.template( 'models-list' );

            target.innerHTML = template({
                body: response.car.models['0'].code,
                markets: response.car.models['0'].models,
                oid: response.oid,
            });
            $('.model-item').off('click');
            $('.model-item').on('click', options);
        }

        function options(e) {
            let oid = JSON.parse(e.currentTarget.dataset.oid);

            if ( _.isEmpty( App.options[ oid.model ]) ) {
                wp.ajax.send( 'get_options', {
                    data: { oid: oid },
                } ).then( (response) => {
                    App.options[ response.oid.model ] = response;
                    App.options.current = e;
                    optionsSuccess(response);
                } );
            } else {
                optionsSuccess(App.options[ oid.model ]);
            }
        }
        function optionsSuccess(response) {
            let template = wp.template( 'options-list' ),
                target = document.querySelector('.modal-content');

            target.innerHTML = template({
                oid: response.oid,
                options: response.car.options,
            });
            $('.modal').modal('show');
            $('.option-link').off('click');
            $('.option-link').on('click', production);
        }

        function production(e) {
            let oid = JSON.parse(e.currentTarget.dataset.oid);

            if ( _.isEmpty(App.productions[ oid.rule + oid.transmission ]) ) {
                wp.ajax.send( 'get_production', {
                    data: { oid: oid },
                } ).then( (response) => {
                    App.productions[ response.oid.rule + response.oid.transmission ] = response;
                    productionSuccess(response);
                } );
            } else {
                productionSuccess(App.models[ oid.rule + oid.transmission ]);
            }
        }
        function productionSuccess(response) {
            let template = wp.template( 'production-list' ),
                target = document.querySelector('.modal-content');

            target.innerHTML = template({
                oid: response.oid,
                production: response.car.production,
                url: response.car.url,
            });
        }

    },
};
