/*
 * cookie javascript library v 0.2
 * http://code.google.com/p/jscookie/
 *
 * Copyright (c) 2009 Evgeniy Tyurin
 * Licensed under the GPL licenses
 * http://www.gnu.org/licenses/gpl-3.0.txt
 *
 * Date: 2009-11-03 15:48:12
 */

window.cookie =
{
  set: function(key, value, expires, path, domain, secure)
  {
    var sCookie = key+'='+escape(value)+'; ';

    if(expires !== undefined) {
      var date = new Date();
      date.setTime(date.getTime()+(expires*24*60*60*1000));
      sCookie+= 'expires='+date.toGMTString()+'; ';
    }

    sCookie+= (path === undefined) ? 'path=/;' : 'path='+path+'; ';
	sCookie+= (domain === undefined) ? '' : 'domain='+domain+'; '
	sCookie+= (secure === true) ? 'secure; ' : '';

    document.cookie = sCookie;
  },

  get: function(sKey)
  {
    var sValue = '';
    var sKeyEq = sKey+ '=';
    var aCookies = document.cookie.split(';');

    for(var iCounter = 0, iCookieLength = aCookies.length; iCounter < iCookieLength; iCounter++) {
      while(aCookies[iCounter].charAt(0) === ' ') {aCookies[iCounter] = aCookies[iCounter].substring(1);}
      if(aCookies[iCounter].indexOf(sKeyEq) === 0) {
        sValue = aCookies[iCounter].substring(sKeyEq.length);
      }
    }

    return unescape(sValue);
  },

  remove: function(key)
  {
    cookie.set(key, '', -1);
  },

  clear: function()
  {
    var aCookies = document.cookie.split(';');
    
    for(var iCounter = 0, iCookieLength = aCookies.length; iCounter < iCookieLength; iCounter++) {
      while(aCookies[iCounter].charAt(0) === ' ') {aCookies[iCounter] = aCookies[iCounter].substring(1);}
      var iIndex = aCookies[iCounter].indexOf('=', 1);
      if(iIndex > 0) {
        cookie.set(aCookies[iCounter].substring(0, iIndex), '', -1);
      }
    }
  },

  isEnabled: function()
  {
    cookie.set('test_cookie', 'test');

    var val = (cookie.get('test_cookie') === 'test') ? true : false;

    cookie.remove('test_cookie');

    return val;
  }
};