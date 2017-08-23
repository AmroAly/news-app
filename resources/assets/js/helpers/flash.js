export default {
    state: {
        success: null,
        error: null
    },
    setSuccess(message, hide = true) {
        this.state.success = message
        if(hide)
            setTimeout(() => {
                this.removeSuccess()
            }, 5000)
    },
    setError(message) {
        this.state.error = message
        setTimeout(() => {
            this.removeError()
        }, 5000)
    } ,
    removeSuccess() {
        this.state.success = null
    },
    removeError() {
        this.state.error = null
    }
}
