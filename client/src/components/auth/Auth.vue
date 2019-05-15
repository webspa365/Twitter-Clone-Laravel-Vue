<template>
  <div class='auth'>
    <div class='left'>
      <ul>
        <li>
          <i class='fa fa-search'></i>
          <span>Follow your interests.</span>
        </li>
        <li>
          <i class='fa fa-user-o'></i>
          <span>Hear what people are talking about.</span>
        </li>
        <li>
          <i class='fa fa-comment-o'></i>
          <span>Join the conversation.</span>
        </li>
      </ul>
      <div class='bg'><i class='fa fa-twitter'></i></div>
    </div>
    <div class='right' >
      <div class='wrapper' v-if='this.type === "Auth"'>
        <i class='fa fa-twitter'></i>
        <p>See what’s happening in<br /> the world right now</p>
        <h1>Join Twitter today.</h1>
        <button class='btn btn-primary' @click='switch_form("SignUp")'>Sign Up</button>
        <button class='btn btn-default' @click='switch_form("LogIn")'>Log In</button>
      </div>
      <SignUp v-else-if='this.type === "SignUp"' :switch_form='switch_form' />
      <LogIn v-else-if='this.type === "LogIn"' :switch_form='switch_form' />

    </div>
  </div>
</template>

<script>
require('./Auth.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import LogIn from '@/components/auth/LogIn'
import SignUp from '@/components/auth/SignUp'

export default {
  name: 'Auth',
  components: {
    LogIn,
    SignUp
  },
  data: function () {
    return {
      msg: '',
      type: 'Auth'
    }
  },

  methods: {
    post_data: function(e) {

    },

    switch_form: function(type) {
      this.type = type;

    }
  },

  beforeMount() {
    if(this.$store.state.loggedIn && this.$store.state.account) {
      this.$router.push('/profile?user='+this.$store.state.account.username);
    }

  },

  beforeDestroy() {

  }
}
</script>
