import { apiFetchPostOpen, apiFetchMyPost } from '../api/posts'

const POST_OPEN_FETCH = 'post_fetch'
const POST_MY_FETCH = 'my_post_fetch'
const POST_OPEN_UP = 'post_open_up'
const POST_OPEN_DOWN = 'post_open_down'

const state = {
    postOpen: 0,
    myPost: 0
}

const mutations = {
    [POST_OPEN_FETCH](state, postOpen) {
        return state.postOpen = postOpen
    },
    [POST_OPEN_UP](state) {
        return state.postOpen++;
    },
    [POST_OPEN_DOWN](state) {
        return state.postOpen--;
    },
    [POST_MY_FETCH](state, myPost) {
        return state.myPost = myPost
    },
}

const actions = {
    async actionPostOpenFetch({ commit }) {
        let response = await apiFetchPostOpen()

        if (response.status == 200 ) {
            return commit(POST_OPEN_FETCH, response.data)
        }
    },
    actionPostOpenUpFetch({ commit }){
        return commit(POST_OPEN_UP)
    },
    actionPostOpenDownFetch({ commit }){
        return commit(POST_OPEN_DOWN)
    },
    async actionMyPostFetch({ commit }) {
        let response = await apiFetchMyPost()

        if (response.status == 200 ) {
            return commit(POST_MY_FETCH, response.data)
        }
    },
}

export default {
    state,
    actions,
    mutations
}