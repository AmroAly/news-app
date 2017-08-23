import { post } from '../helpers/api'

export default {
    state: {
        'api_token': null,
        'user_id': null,
        'authenticated': false
    },
    initialize() {
        this.state.api_token = localStorage.getItem('api_token')
        this.state.user_id = parseInt(localStorage.getItem('user_id'))

        const data = {'token': this.state.api_token, 'user_id': this.state.user_id}
        post(`/api/check`, data)
            .then((res) => {
                if (res.data.authenticated) {
                    this.state.authenticated = true
                }
            })
            .catch((err) =>{
                if (err.response.status === 401) {
                    this.state.authenticated = false
                    this.remove()
                }
            })
    },
    set(api_token, user_id) {
        localStorage.setItem('api_token', api_token)
        localStorage.setItem('user_id', user_id)
        this.initialize()
    },
    remove() {
        localStorage.removeItem('api_token')
        localStorage.removeItem('user_id')
        this.initialize()
    },
    check() {
        post(`/api/check`, data)
            .then((res) => {
                if (res.data.authenticated) {
                    return true
                }
                return false
            })
    }
}
