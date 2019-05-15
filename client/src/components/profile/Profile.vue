<template>
  <div class='profile'>
    <div class='bg'>
     <img :src="profile.dir+'/bg/'+profile.bg" onerror="this.style.display='none'" />
    </div>
    <div class='nav'>
     <div class='container'>
       <router-link class='avatar' :to="'/profile?user='+profile.username">
         <img :src="profile.dir+'/avatar/'+profile.avatar" onerror="this.style.display='none'" />
       </router-link>
       <ul class='profile_ul'>
         <li class='active' @click='click_li(0)'>
           <router-link :to="'/profile?user='+profile.username">
             <span>Tweets</span>
             <span>{{profile.tweets}}</span>
           </router-link>
         </li>
         <li @click='click_li(1)'>
           <router-link :to="'/profile/following?user='+profile.username">
             <span>Following</span>
             <span>{{profile.following}}</span>
           </router-link>
         </li>
         <li @click='click_li(2)'>
           <router-link :to="'/profile/followers?user='+profile.username">
             <span>Followers</span>
             <span>{{profile.followers}}</span>
           </router-link>
         </li>
         <li @click='click_li(3)'>
           <router-link :to="'/profile/likes?user='+profile.username">
             <span>Likes</span>
             <span>{{profile.likes}}</span>
           </router-link>
         </li>
       </ul>
        <button v-if='profile.username === this.$store.state.account.username'
        class='btn btn-default edit' @click='edit_profile()'>
         Edit Profile
        </button>
        <button v-else-if='profile.followed === 1'
        class='btn btn-primary follow followed' @click='follow_user()'></button>
        <button v-else-if='profile.followed === 0'
        class='btn btn-primary follow' @click='follow_user()'>Follow</button>
     </div>
   </div>
     <div class='main container'>
       <div class='row'>
         <div class='left col-lg-3'>
           <div class='info'>
              <h1>
                <span class='name'>{{profile.name}}</span>
                <span class='username'>@{{profile.username}}</span>
              </h1>
              <p class='bio'>{{profile.bio}}</p>
              <div class='date'>{{date}}</div>
            </div>
          </div>
          <div class='col-lg-9'>
            <div class='col-lg-8' v-if="type === 'tweets' || type === 'likes'">
              <Tweets :skip='this.skip' :get_tweets='this.get_tweets' :type='this.type' :get_likes='this.get_likes' />
            </div>
            <div class='col-lg-4' v-if="type === 'tweets' || type === 'likes'">
            </div>
            <ul class='users' v-else>
              <li v-for='u in users' :key='u.id'>
                <User :data='u' />
              </li>
            </ul>
          </div>
        </div>
       </div>
     </div>
   </div>
</template>

<script>
require('./Profile.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

import Tweets from '@/components/tweets/Tweets'
import User from '@/components/profile/User'

var months = ["January", "February", "March", "April", "May", "June",
  "July", "August", "September", "October", "November", "December"
];

export default {
  name: 'Profile',
  components: {
    Tweets,
    User
  },
  data: function () {
    return {
      msg: '',
      profile: {},
      date: '',
      skip: 0,
      rSkip: 0,
      type: 'tweets',
      users: []

    }
  },
  computed: {
    setProfile: function() {
      this.profile = this.$store.state.profile;
    },
    setUsers: function() {
      this.users = this.$store.state.users;
    }
  },
  methods: {
    click_li: function(index) {
      this.skip = 0;
      this.rSkip = 0;
      this.$store.commit('tweets', []);
      this.$root.$emit('updateTweets');
      if(index === 0) this.get_tweets();
      else if(index === 1) this.get_following();
      else if(index === 2) this.get_followers();
      else if(index === 3) this.get_likes();
    },

    get_profile: function() {
      var username = window.location.search.split('?user=')[1];
      if(!username) return;
      if(this.$store.state.profile.username === username) {
        //this.profile = this.$store.state.profile;
        //return;
      }
      if(localStorage.getItem('jwtToken')) {
        http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      }
      http.get('/api/profile/'+username)
      .then((res) => {
        console.log('/users/get='+JSON.stringify(res.data));
        if(res.data.success) {
          var user = res.data.user;
          user.dir = server.url+'/storage/media/'+user.id;
          if(user.avatar) {
            var ext = user.avatar.split('.').pop();
            user.avatarT = user.dir+'/avatar/thumbnail.'+ext;
          }
          this.profile = user;
          this.$store.commit('profile', user);
          if(this.$store.state.account && this.$store.state.account.username === this.$store.state.profile.username) {
            this.$store.commit('account', user);
          }
        }
      });
    },

    edit_profile: function() {
      this.$router.push('/editProfile?user='+this.profile.username);
    },

    get_tweets: function() {
      this.type = 'tweets';
      this.$store.commit('loading', true);
      if(localStorage.getItem('jwtToken')) {
        http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      }
      http.get('/api/tweets/'+this.$store.state.profile.id, {params: {skip: this.skip, rSkip: this.rSkip}})
      .then((res) => {
        //console.log('/api/tweets/:id='+JSON.stringify(res.data));
        if(res.data.success) {
          var tweets = res.data.tweets;
          if(res.data.avatars) {
            var avatars = res.data.avatars;
            for(var t of tweets) {
              if(avatars[t.username]) {
                var ext = avatars[t.username].split('.').pop();
                t.avatar = server.url+'/storage/media/'+t.userId+'/avatar/thumbnail.'+ext;
              }
              // update skip
              if(t.retweetedBy === '') {
                this.skip++;
              } else {
                this.rSkip++;
              }
            }
          }
          var oldTweets = this.$store.state.tweets;
          tweets = oldTweets.concat(tweets);

          this.$store.commit('tweets', tweets);
          this.$root.$emit('updateTweets');
          /*
          setTimeout(() => {
            window.scrollTo(0,document.body.scrollHeight);
          }, 1000);
          */
        }
        this.$store.commit('loading', false);
      }).catch((err) => {
        this.$store.commit('loading', false);
      });
    },

    get_following: function() {
      this.type = 'following';
      this.$store.commit('loading', true);
      this.$store.commit('users', []);
      http.get('/api/relationships/following/'+this.profile.id)
      .then((res) => {
        //console.log('/api/relationships/following='+JSON.stringify(res.data));
        if(res.data.success) {
          var users = res.data.users;
          for(var u of users) {
            u.dir = server.url+'/storage/media/'+u.id;
            if(u.bg) {
              var ext = u.bg.split('.').pop();
              u.bg = u.dir+'/bg/thumbnail.'+ext;
            }
            if(u.avatar) {
              var ext = u.avatar.split('.').pop();
              u.avatar = u.dir+'/avatar/thumbnail.'+ext;
            }
          }
          this.$store.commit('users', users);
          this.setUsers();
        }
        this.$store.commit('loading', false);
      }).catch((err) => {
        this.$store.commit('loading', false);
      });
    },

    get_followers: function() {
      console.log('get_followers()');
      this.type = 'followers';
      this.$store.commit('loading', true);
      this.$store.commit('users', []);
      if(localStorage.getItem('jwtToken')) {
        http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      }
      http.get('/api/relationships/followers/'+this.profile.id)
      .then((res) => {
        console.log('/api/relationships/followers='+JSON.stringify(res.data));
        this.$store.commit('loading', false);
        if(res.data.success) {
          var users = res.data.users;
          for(var u of users) {
            u.dir = server.url+'/storage/media/'+u.id;
            if(u.bg) {
              var ext = u.bg.split('.').pop();
              u.bg = u.dir+'/bg/thumbnail.'+ext;
            }
            if(u.avatar) {
              var ext = u.avatar.split('.').pop();
              u.avatar = u.dir+'/avatar/thumbnail.'+ext;
            }
          }
          this.$store.commit('users', users);
          this.setUsers();
        }
        this.$store.commit('loading', false);
      }).catch((err) => {
        this.$store.commit('loading', false);
      });
    },

    get_likes: function() {
      console.log('get_likes()');
      this.type = 'likes';
      this.$store.commit('loading', true);
      if(localStorage.getItem('jwtToken')) {
        http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      }
      http.get('/api/likedTweets/'+this.$store.state.profile.id, {params: {skip: this.skip, rSkip: this.rSkip}})
      .then((res) => {
        //console.log('/api/likedTweets/:id='+JSON.stringify(res.data));
        if(res.data.success) {
          var tweets = res.data.tweets;
          if(res.data.avatars) {
            var avatars = res.data.avatars;
            for(var t of tweets) {
              if(avatars[t.username]) {
                var ext = avatars[t.username].split('.').pop();
                t.avatar = server.url+'/storage/media/'+t.userId+'/avatar/thumbnail.'+ext;
              }
              // update skip
              if(t.retweetedBy === '') {
                this.skip++;
              } else {
                this.rSkip++;
              }
            }
          }
          var oldTweets = this.$store.state.tweets;
          tweets = oldTweets.concat(tweets);
          this.$store.commit('tweets', tweets);
          this.$root.$emit('updateTweets');
        }
        this.$store.commit('loading', false);
      }).catch((err) => {
        this.$store.commit('loading', false);
      });
    },

    follow_user() {
      console.log('follow_user()');
      if(!this.$store.state.loggedIn || !localStorage.getItem('jwtToken')) {
        this.$router.push('/');
        return;
      }
      http.defaults.headers.common['Authorization'] = 'Bearer '+localStorage.getItem('jwtToken');
      http.post('/api/relationships', {userId: this.profile.id})
      .then((res) => {
        console.log('/relationships='+JSON.stringify(res.data));
        if(res.data.success) {
          if(res.data.followed) {
            this.profile.followed = 1;
          } else {
            this.profile.followed = 0;
          }
          this.$store.state.account.following = res.data.following;
          if(this.$store.state.account.id === this.$store.state.profile.id) {
            this.profile.following = res.data.following;
          } else {
            this.profile.followers = res.data.followers;
          }
        }
      });
    }
  },

  watch: {
    $route() {
      console.log('$route()');
      var username = window.location.search.split('?user=')[1];
      if(!username) return;
      var wait = 0;
      if(username !== this.profile.username) {
        wait = 500;
        this.get_profile();
        this.$store.commit('tweets', []);
      }
      /*
      if(window.location.href.indexOf('/profile?user=') > -1) {
        setTimeout(() => {this.get_tweets();}, wait);
      }

      else if(window.location.href.indexOf('/profile/following?') > -1) {
        setTimeout(() => {this.get_following();}, wait);

      }
      else if(window.location.href.indexOf('/profile/followers?') > -1) {
        setTimeout(() => {this.get_followers();}, wait);
      }
      else if(window.location.href.indexOf('/profile/likes?') > -1) {
        setTimeout(() => {this.get_likes();}, wait);
      }
      */
    }
  },

  beforeMount() {
    this.get_profile();
    this.$store.commit('tweets', []);
    this.get_tweets();
    if(this.$store.state.profile) {
      var d = new Date(this.$store.state.profile.created_at);
      console.log('d='+d);
      if(d) this.date = 'Member since '+ d.getFullYear()+' '+months[d.getMonth()];
    }
  },

  beforeDestroy() {

  }
}
</script>
