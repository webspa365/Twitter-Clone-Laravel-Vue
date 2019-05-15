<template>
  <div class='whoToFollow'>
    <h2>Who to follow</h2>
    <ul>
      {users}
    </ul>
  </div>
</template>

<script>
require('./WhoToFollow.css')
import {http} from '@/modules/http'
import {server} from '@/modules/server'

export default {
  name: 'WhoToFollow',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function(e) {
      var comp = this;
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
