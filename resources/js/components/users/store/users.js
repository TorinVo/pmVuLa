import { apiFetchUser } from '../api/users'

const USER_FETCH = 'user_fetch'

const state = {
    users: {}
}

const mutations = {
    [USER_FETCH](state, users) {
        return state.users = users
    },
}

const actions = {
    async actionUserFetch({ commit }) {
        let response = await apiFetchUser()

        if (response.status == 200 ) {
            return commit(USER_FETCH, response.data)
        }
    },
}

export default {
    state,
    actions,
    mutations
}