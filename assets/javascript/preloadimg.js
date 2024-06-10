// exemplo de como usar. obs: por no topo debaixo do jquery (obrigatorio para funcionar).

// <img src="imagems/default.jpg" 
//     data-srcset="imagem/avatar.jpg" 
//     onload="preloadImage(this)"
// />

function preloadImage(img) {
  // Use AJAX para carregar a imagem mais pesada em segundo plano
  var imgEl = $(img);
  
  var src = imgEl.attr('src');
  var srcset = imgEl.attr('data-srcset');

  if (src !== srcset && srcset) { // para evitar um looping
    $.ajax({
      url: srcset,
      type: 'HEAD',
      success: function() { // assim faz com que se a imagem estiver quebrada ele não substitua.
          // Quando a imagem for carregada com sucesso
          // Atualize o atributo src da tag img para exibir a imagem pré-carregada
          imgEl.attr('src', srcset);
      }
    });
  }
}


function defaultImage(img) {
  // Use AJAX para carregar a imagem mais pesada em segundo plano
  var imgEl = $(img);
  
  var src = imgEl.attr('src');
  var srcset = imgEl.attr('data-srcset');

  if (src !== srcset && srcset) { // para evitar um looping
    imgEl.attr('src', srcset);
  }
}
