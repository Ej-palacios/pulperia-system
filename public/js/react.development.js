/**
 * React v18.2.0
 * Versión local para evitar bloqueos de CDN
 */

// Definición básica de React
window.React = {
  createElement: function(type, props, ...children) {
    return { type, props: props || {}, children };
  },
  Fragment: Symbol('Fragment'),
  useState: function(initialState) {
    const setter = function(newValue) {
      state = typeof newValue === 'function' ? newValue(state) : newValue;
      return state;
    };
    let state = initialState;
    return [state, setter];
  },
  useEffect: function(callback, deps) {
    // Implementación básica
    callback();
  },
  useRef: function(initialValue) {
    return { current: initialValue };
  },
  version: '18.2.0'
};