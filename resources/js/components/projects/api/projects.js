export function apiFetchProjects(page) {
    if(page){
        return axios.get('/api/project?page=' + page)
            .then(
                response => response
            )
    }
    
    return axios.get('/api/project')
        .then(response => response)
}

export function apiFetchAllProjects() {
    return axios.get('/api/project', {
        params: {
          type: 'all'
        }
    })
    .then(response => response)
}

