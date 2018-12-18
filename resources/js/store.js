import Vue from 'vue';
import Vuex from 'vuex';

import storeProject from './components/projects/store/projects';
import storePost from './components/posts/store/posts';
import storeUser from './components/users/store/users';

Vue.use(Vuex);

//import router from './router';

export default new Vuex.Store({
    modules: {
        storeProject,
        storePost,
        storeUser
    }
});
  