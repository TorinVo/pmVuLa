export default class Gate{
    constructor(user){
        this.user = user;
    }

    isAdmin(){
        let check = false
        if(this.user.roles) {
            this.user.roles.forEach(role => {
                if(role.name === 'Admin') {
                    check = true
                }
            })
        }
        return check
    }

    isDesigner(){
        let check = false
        if(this.user.roles) {
            this.user.roles.forEach(role => {
                if(role.name === 'Designer') {
                    check = true
                }
            })
        }
        return check
    }

    isDeveloper(){
        let check = false
        if(this.user.roles) {
            this.user.roles.forEach(role => {
                if(role.name === 'Developer') {
                    check = true
                }
            })
        }
        return check
    }

    isAdminOrDesigner(){
        let check = false
        if(this.user.roles) {
            this.user.roles.forEach(role => {
                if(role.name === 'Designer' || role.name === 'Admin') {
                    check = true
                }
            })
        }
        return check

    }
    
    idLogin(){
        return this.user.id
    }
}