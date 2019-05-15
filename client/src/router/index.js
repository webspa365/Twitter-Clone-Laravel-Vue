import Vue from 'vue'
import Router from 'vue-router'
import Auth from '@/components/auth/Auth'
import Home from '@/components/home/Home'
import Moments from '@/components/moments/Moments'
import Notifications from '@/components/notifications/Notifications'
import Messages from '@/components/messages/Messages'
import Profile from '@/components/profile/Profile'
import EditProfile from '@/components/profile/EditProfile'

Vue.use(Router)

export default new Router({
  routes: [
    /*
    {
      path: '/',
      name: 'HelloWorld',
      component: HelloWorld
    },
    */
    {
      path: '/',
      name: 'Auth',
      component: Auth
    },

    {
      path: '/profile',
      name: 'Profile',
      component: Profile
    },

    {
      path: '/profile/following',
      name: 'Profile',
      component: Profile
    },

    {
      path: '/profile/followers',
      name: 'Profile',
      component: Profile
    },

    {
      path: '/profile/likes',
      name: 'Profile',
      component: Profile
    },

    {
      path: '/profile/replies',
      name: 'Profile',
      component: Profile
    },

    {
      path: '/profile/media',
      name: 'Profile',
      component: Profile
    },

    {
      path: '/editProfile',
      name: 'EditProfile',
      component: EditProfile
    },

    {
      path: '/home',
      name: 'Home',
      component: Home
    },

    {
      path: '/moments',
      name: 'Moments',
      component: Moments
    },

    {
      path: '/notifications',
      name: 'Notifications',
      component: Notifications
    },

    {
      path: '/messages',
      name: 'Messages',
      component: Messages
    }
  ],
  mode: 'history'
})
