export default {
    init ( { commit }, component ) {
        Vue.http.get('/api/' + component)
            .then( response => response.json() )
            .then( items => commit('INIT_' + component.toUpperCase(), items ) )
    },

    create ( { commit }, payload ) {
        prepare(payload)
        Vue.http.post('/api/' + payload.component, JSON.stringify(payload.item))
            .then( response => response.json() )
            .then( response => {
                commit('CREATE_' + payload.component.toUpperCase(), response )
                $('#modal').modal('hide')
                clear(payload)
            })
    },

    update ( { commit }, payload ) {
        prepare(payload)
        Vue.http.put('/api/' + payload.component + '/' + payload.item.id, JSON.stringify(payload.current))
            .then( response => response.json() )
            .then( current => commit('UPDATE_' + payload.component.toUpperCase(), { item: payload.item, current } ) )
    },

    destroy ( { commit }, payload ) {
        Vue.http.delete('/api/' + payload.component + '/' + payload.item.id).then(
            () => { commit('DESTROY_' + payload.component.toUpperCase(), payload.item) }
        )
    },

    edit ( { commit }, payload ) { commit('EDIT', payload ) },

    cancel ( { commit }, payload ) { commit('CANCEL', payload ) },

    clear ( { commit }, payload ) { clear(payload) },

}
