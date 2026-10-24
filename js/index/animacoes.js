// elementos
const perguntas = document.querySelectorAll('.faq-pergunta');
const header = document.querySelector('header');
const container_comidas = document.querySelector('#container-comidas');
const container_cards_display = document.querySelector('#container-cards-display');
const container_areas_bangalos = document.querySelector('#areas-bangalos-container');
const container_reserva = document.querySelector('#container-reserva');
const container_depoimentos = document.querySelector('#container-depoimentos');

// Função do Observer Otimizada
function observarContainers(container, classlist) {
  if (!container) return;

  const options = {
    root: null,
    rootMargin: '0px 0px -20px 0px', // Força o disparo ligeiramente antes de entrar na tela
    threshold: 0.05 // Valor menor para telas pequenas como o iPhone SE
  };

  const observer = new IntersectionObserver((entries, observerInstance) => {
    entries.forEach((entry) => {
      if (entry.isIntersecting) {
        entry.target.classList.add(classlist);
        observerInstance.unobserve(entry.target);
      }
    });
  }, options);

  observer.observe(container);
}

// Aplica o observer com verificação de segurança
observarContainers(container_comidas, 'visivel');
observarContainers(container_areas_bangalos, 'visivel');
observarContainers(container_reserva, 'visivel');
observarContainers(container_depoimentos, 'visivel');

// Slide Infinito
if (container_cards_display) {
  const container = container_cards_display;

  // 1. Cria uma wrapper interna para mover os elementos livremente sem dependência do scroll
  const track = document.createElement('div');
  track.style.display = 'flex';
  track.style.width = 'max-content';
  track.style.justifyContent = 'space-between';
  track.style.gap = '20px';
  track.style.willChange = 'transform'; // Otimiza performance na GPU

  // Move os cards para dentro do track
  while (container.firstChild) {
    track.appendChild(container.firstChild);
  }

  // Duplica os elementos para criar o efeito infinito
  const originalCards = Array.from(track.children);
  originalCards.forEach((card) => {
    track.appendChild(card.cloneNode(true));
  });

  container.appendChild(track);
  container.style.overflow = 'hidden';

  // 2. Animação por Transform
  const velocidadePX = 40; // Pixels por segundo
  let posicao = 0;
  let ultimaHora = null;

  function animar(tempoAtual) {
    if (!ultimaHora) ultimaHora = tempoAtual;
    const deltaTime = (tempoAtual - ultimaHora) / 1000;
    ultimaHora = tempoAtual;

    // Largura total da metade original
    const metadeDaLargura = track.scrollWidth / 4;

    posicao -= velocidadePX * deltaTime;

    // Quando mover a largura exata da metade, reseta sem quebra visual
    if (Math.abs(posicao) >= metadeDaLargura) {
      posicao += metadeDaLargura;
    }

    track.style.transform = `translateX(${posicao}px)`;

    requestAnimationFrame(animar);
  }

  requestAnimationFrame(animar);
}

// Animação Header
if (window.innerWidth >= 768) {
  window.document.addEventListener('scroll', () => {
    if (window.scrollY <= 50) {
      header.style = 'filter:opacity(1); transition:0.5s filter ease-in-out;   background: linear-gradient(to bottom, rgba(0, 0, 0, 0.709), rgba(3, 30, 31, 0.19));';
    } else {
      header.style = 'filter:opacity(0); transition:0.5s filter ease-in-out;';
    }
  });

  header.addEventListener('mouseenter', () => {
    if (window.scrollY > 50) {
      header.style = 'background: linear-gradient(to bottom, rgb(0, 0, 0), #031723); border-bottom: 2px orange solid; filter:opacity(1); transition:0.5s filter ease-in-out;';
    }
  });

  header.addEventListener('mouseleave', () => {
    if (window.scrollY > 50) {
      header.style = 'filter:opacity(0); transition:0.5s filter ease-in-out;';
    }
  });
} else {
  window.document.addEventListener('scroll', () => {
    if (window.scrollY <= 250) {
      header.style = 'background: transparent; transition: 0.5s background ease-in-out;';
    } else {
      header.style = 'background: linear-gradient(to bottom, rgb(0, 0, 0), #031723); border-bottom: 2px orange solid; transition: 0.5s background border ease-in-out;';
    }
  });
}

// Respostas Perguntas Frequentes
perguntas.forEach(pergunta => {
  pergunta.addEventListener('click', () => {
    const itemAtual = pergunta.parentElement;

    document.querySelectorAll('.faq-item').forEach(item => {
      if (item !== itemAtual) {
        item.classList.remove('ativo');
      }
    });

    itemAtual.classList.toggle('ativo');
  });
});