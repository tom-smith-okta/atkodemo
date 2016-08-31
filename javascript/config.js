(function (root, factory) {
    if (typeof define === 'function' && define.amd) {
        define([], factory);
    } else if (typeof module === 'object' && module.exports) {
        module.exports = factory();
    } else {
        root.OktaConfig = factory();
  }
}(this, function () {

    return {
      // orgUrl: 'https://oidcdemos.oktapreview.com',
      orgUrl: 'https://tomco.okta.com',

      // clientId: 'guHLNDnxiATk0zYXUcHZ',

      clientId: 'Ll8JN6oiAJ04jOxfKVpT',
      
      scope: ['openid', 'email', 'profile', 'phone', 'address', 'groups'],
      redirectUri: 'http://localhost:8888'
    };

}));
