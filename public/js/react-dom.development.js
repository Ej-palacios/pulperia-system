/**
 * ReactDOM v18.2.0
 * Versión local para evitar bloqueos de CDN
 */

// Definición básica de ReactDOM
window.ReactDOM = {
  createRoot: function(container) {
    return {
      render: function(element) {
        if (typeof element === 'object' && element !== null) {
          // Implementación básica para renderizar elementos
          container.innerHTML = '';
          
          // Crear un elemento DOM basado en el tipo
          const domElement = document.createElement(element.type || 'div');
          
          // Aplicar propiedades
          if (element.props) {
            Object.keys(element.props).forEach(prop => {
              if (prop === 'className') {
                domElement.className = element.props[prop];
              } else if (prop === 'style') {
                Object.assign(domElement.style, element.props[prop]);
              } else if (prop.startsWith('on') && typeof element.props[prop] === 'function') {
                const eventName = prop.substring(2).toLowerCase();
                domElement.addEventListener(eventName, element.props[prop]);
              } else {
                domElement.setAttribute(prop, element.props[prop]);
              }
            });
          }
          
          // Añadir hijos
          if (element.children) {
            element.children.forEach(child => {
              if (typeof child === 'string' || typeof child === 'number') {
                domElement.appendChild(document.createTextNode(child));
              } else if (child) {
                // Renderizar hijos recursivamente
                const childDom = document.createElement(child.type || 'div');
                domElement.appendChild(childDom);
              }
            });
          }
          
          container.appendChild(domElement);
        }
      }
    };
  },
  render: function(element, container) {
    this.createRoot(container).render(element);
  },
  version: '18.2.0'
};