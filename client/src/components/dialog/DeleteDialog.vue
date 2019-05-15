<template>
  <div class='deleteDialog' @click='close_dialog($event)'>
        <div class='dialog_wrapper'>
          <div class='header'>
            Delete tweet?
          </div>
          <div class='body'>
            <h6>@{{this.$store.state.deleteDialog.username}}</h6>
            <p>{{this.$store.state.deleteDialog.tweet}}</p>
            <div class='msg'>{{msg}}</div>
          </div>
          <div class='footer'>
            <button class='btn btn-danger' @click='delete_tweet()'>Delete</button>
            <button class='btn btn-default closeButton' @click='close_dialog($event)'>Cancel</button>
          </div>
        </div>
      </div>
</template>

<script>
require('./DeleteDialog.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'DeleteDialog',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    delete_tweet: function() {
      if(!localStorage.getItem('jwtToken') || !this.$store.state.loggedIn) {
        this.$router.push('/');
        this.msg = '';
        _('.deleteDialog').style.display = 'none';
        return;
      }
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      var tweet = this.$store.state.deleteDialog;
      http.delete('api/tweets/'+tweet.id)
      .then((res) => {
        console.log('tweets/remove='+JSON.stringify(res.data));
        //this.props.set_menu(false);
        if(res.data.success) {
          // update tweets
          var tweets = this.$store.state.tweets;
          var arr = [];
          for(var t of tweets) {
            if(t.id != res.data.deletedId) {
              arr.push(t);
            }
          }
          this.$store.commit('tweets', arr);
          this.$root.$emit('updateTweets');

          // update account
          var account = this.$store.state.account;
          account.tweets = res.data.count;
          this.$store.commit('account', account);

          // update profile
          if(this.$store.state.profile.username == this.$store.state.account.username) {
            var profile = this.$store.state.profile;
            profile.tweets = res.data.count;
            this.$store.commit('profile', profile);
          }

          // close dialog
          this.msg = '';
          _('.deleteDialog').style.display = 'none';
        } else {
          this.msg = res.data.msg;
        }
      }).catch((err) => {
        this.msg = err;
      });
    },

    close_dialog: function(e) {
      var classList = e.target.classList.toString();
      if(classList.indexOf('deleteDialog') > -1 || classList.indexOf('closeButton') > -1) {
        this.msg = '';
        _('.deleteDialog').style.display = 'none';
      }
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
