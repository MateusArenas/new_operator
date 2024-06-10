$(document).ready(function() {
  // Array para armazenar elementos com popover
  var popoverList = [];

  // Função para inicializar o popover
  function setPopover (el) {
    var popover = new bootstrap.Popover(el);
    popoverList.push(el);
    return popover;
  }

  // Executar a inicialização do popover quando o documento é carregado
  $('[data-bs-toggle="popover"]').each(function() {
    setPopover(this);
  });

  // Adicionar manipuladores de eventos para hover e clique
  $(document).on('click mouseenter mouseleave', '[data-bs-toggle="popover"]', function(event) {
    // A propriedade event.type contém o tipo do evento acionado
    var eventType = event.type;
    // Verificar se o elemento já está na lista popoverList
    var hasInList = popoverList.find(el => el === this);
    // Se não estiver na lista, inicialize o popover e adicione-o à lista
    if (!hasInList) { 
      var popover = setPopover(this); 

      var eventTriggers = $(this).attr('data-bs-trigger');

      if (eventType === 'mouseenter') eventType = 'hover';

      if (eventTriggers.includes(eventType)) {
        // Aciona caso o evento seja o ideal
        popover.show();
      }

    }
  });
  
});