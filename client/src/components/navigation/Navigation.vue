<template>
  <nav class='navigation'>
    <ul>
      <li class='left' @click='click_link(0)'>
        <router-link to='/home' ><i class='fa fa-home'></i><span>Home</span></router-link>
      </li>
      <li class='left' @click='click_link(1)'>
        <router-link to='/moments' ><i class='fa fa-bolt'></i><span>Moments</span></router-link>
      </li>
      <li class='left' @click='click_link(2)'>
        <router-link to='/notifications' ><i class='fa fa-bell-o'></i><span>Notifications</span></router-link>
      </li>
      <li class='left' @click='click_link(3)'>
        <router-link to='/messages' ><i class='fa fa-envelope-o'></i><span>Messages</span></router-link>
      </li>
      <li class='center'>
        <div v-if='this.$store.state.loading === false' class='twitter'><i class='fa fa-twitter'></i></div>
        <div v-else-if='this.$store.state.loading === true' class='spinner_wrapper'>
          <div class='spinner'></div>
        </div>
      </li>
      <li class='right li_post'><div @click='open_tweet_dialog()'>Tweet</div></li>
      <li class='right li_user' @click='show_menu()'>
        <div>
          <i v-if='this.$store.state.navIcon == ""' class='fa fa-user'></i>
          <img :src='this.$store.state.navIcon' />
        </div>
        <Menu />
      </li>
      <li class='right li_search'><Search /></li>
    </ul>
    <TweetDialog />
    <ReplyDialog />
    <DeleteDialog />
    <Dialog />
    <Replies />
  </nav>
</template>

<script>
require('./Navigation.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import Menu from '@/components/navigation/Menu'
import Search from '@/components/navigation/Search'

import TweetDialog from '@/components/dialog/TweetDialog'
import ReplyDialog from '@/components/dialog/ReplyDialog'
import DeleteDialog from '@/components/dialog/DeleteDialog'
import Dialog from '@/components/dialog/Dialog'
import Replies from '@/components/dialog/Replies'


export default {
  name: 'Navigation',
  components: {
    Menu,
    Search,
    TweetDialog,
    ReplyDialog,
    DeleteDialog,
    Dialog,
    Replies
  },

  data: function () {
    return {
      msg: ''
    }
  },

  computed: {
    setLoading: function() {
      this.loading = this.$store.state.loading;
    }
  },

  methods: {
    click_link: function(i) {
    },

    open_tweet_dialog: function() {
      _('.tweetDialog').style.display = 'block';
    },

    show_menu: function() {
      var menu = _('.menu');
      if(menu.style.display === 'none') menu.style.display = 'block';
      else menu.style.display = 'none';
    }
  },
  beforeDestroy() {

  }
}
</script>
