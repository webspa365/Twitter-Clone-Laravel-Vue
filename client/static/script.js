var _id = function(id) {
  var id = id.replace('#', '');
  return document.getElementById(id);
}

var _cn = function(cn) {
  var cn = cn.replace('.', '');
  return document.getElementsByClassName(cn);
}

var _ = function(qs) {
  var ele = document.querySelector(qs);
  return ele;
}

var _all = function(id) {
  return document.querySelectorAll(id);
}


var _css = function(ele, prop) {
  var style;
  if(typeof ele == 'string') {
    ele = _(ele);
  }
  if(!ele.length) {
    style = window.getComputedStyle(ele[0], null);
  } else {
    style = window.getComputedStyle(ele, null);
  }
  return style.getPropertyValue(prop);
}

var set_css = function(ele, obj) {
  if(typeof ele == 'string') {
    ele = _(ele);
  }
  if(!ele.length) {
    Object.assign(ele[0].style, obj);
  } else {
    Object.assign(ele.style, obj);
  }
}

var get_now = function(time) {
  //var d = new Date(time*1000);
  var d = new Date(time);
  return d.getFullYear()+'-'+(d.getMonth()+1)+'-'+d.getDate()+' '+d.getHours()+':'+d.getMinutes()+':'+d.getSeconds();
}

var from_now = function(oldTime) {
  var msPerMinute = 60 * 1000;
  var msPerHour = msPerMinute * 60;
  var msPerDay = msPerHour * 24;
  var msPerMonth = msPerDay * 30;
  var msPerYear = msPerDay * 365;
  var d = new Date();
  var now = d.getTime();
  var n = d.getTimezoneOffset();
  var elapsed = now - (oldTime - (60000*n));
  var val = 0;

  if (elapsed < msPerMinute) {
    val = Math.round(elapsed/1000);
    return val + ' second'+_s(val)+' ago';
  }

  else if (elapsed < msPerHour) {
    val = Math.round(elapsed/msPerMinute);
    return val + ' minute'+_s(val)+' ago';
  }

  else if (elapsed < msPerDay ) {
    val = Math.round(elapsed/msPerHour);
    return val + ' hour'+_s(val)+' ago';
  }

  else if (elapsed < msPerMonth) {
    val = Math.round(elapsed/msPerDay);
    return val + ' day'+_s(val)+' ago';
  }

  else if (elapsed < msPerYear) {
    val = Math.round(elapsed/msPerMonth);
    return  val + ' month'+_s(val)+' ago';
  }

  else {
    val = Math.round(elapsed/msPerYear);
    return val + ' year'+_s(val)+' ago';
  }

  function _s(val) {
    if(val < 2) return '';
    else return 's';
  }
}

function add_event(ele, name, cb) {
  ele.addEventListener(name, function() {
    cb();
  });
}

(function() {

})();

add_event(window, 'load', function() {
  var val = _css(_cn('.container'), 'width');
  console.log('width='+val);
  console.log('length='+_('body .container').length);

  var h = window.innerHeight;
  console.log('h='+h);
  set_css(_('#app'), {'height': h+'px'});
});
