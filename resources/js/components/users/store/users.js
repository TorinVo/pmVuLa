import { apiFetchUser } from '../api/users'

const USER_FETCH = 'user_fetch'
const USER_UNREAD_COUNT = 'update_unread_count'

const state = {
    users: {}
}

const mutations = {
    [USER_FETCH](state, users) {
        return state.users = users
    },
    [USER_UNREAD_COUNT](state, { contact, reset }) {
        return state.users.map((single) => {
            if (single.id !== contact) {
                return single;
            }
            if (reset)
                single.unread = 0;
            else
                single.unread += 1;
            return single;
        });
    }
}

const actions = {
    async actionUserFetch({ commit }) {
        let response = await apiFetchUser()

        if (response.status == 200 ) {
            return commit(USER_FETCH, response.data)
        }
    },
    actionUpdateUnreadCount({ commit }, { contact, reset }){
        return commit(USER_UNREAD_COUNT, { contact, reset })
    }
}

const getters = {
    countUnread(state){
        return _.sumBy(state.users, function(o) { return o.unread; });
    }
}

export default {
    state,
    actions,
    getters,
    mutations
}