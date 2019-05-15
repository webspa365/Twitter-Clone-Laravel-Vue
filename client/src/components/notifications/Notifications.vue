<template>
  <div class='notifications'>
    <h1>Notifications</h1>
  </div>
</template>

<script>
require('./Notifications.css');
import {http} from '@/modules/http';
import {server} from '@/modules/server';

export default {
  name: 'notifications',
  data: function () {
    return {
      msg: ''
    }
  },
  methods: {
    post_data: function() {
    }
  },
  mounted() {

  },
  beforeDestroy() {

  }
}
</script>
