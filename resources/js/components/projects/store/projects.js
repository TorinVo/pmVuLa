import { apiFetchProjects, apiFetchAllProjects } from '../api/projects'

const PROJECT_FETCH = 'project_fetch'

const state = {
    projects: {}
}

const mutations = {
    [PROJECT_FETCH](state, projects) {
        return state.projects = projects
    },
}

const actions = {
    async actionProjectFetch({ commit }, page) {
        let response = await apiFetchProjects(page)

        if (response.status == 200 ) {
            return commit(PROJECT_FETCH, response.data)
        }
    },
    async actionProjectAllFetch({ commit }) {
        let response = await apiFetchAllProjects()

        if (response.status == 200 ) {
            return commit(PROJECT_FETCH, response.data)
        }
    }
}

export default {
    state,
    actions,
    mutations
}