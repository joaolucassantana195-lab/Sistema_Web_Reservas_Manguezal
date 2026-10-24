const header = document.querySelector('header')

let configBanco = null;

//animacao header
if(window.innerWidth >= 768){
  window.document.addEventListener('scroll', () =>{
  if (window.scrollY <= 50){
    header.style = 'filter:opacity(1); border-bottom: 2px orange solid; transition:0.5s filter ease-in-out;'
  }else{
  header.style = 'filter:opacity(0);  transition:0.5s filter ease-in-out; background: linear-gradient(to bottom, rgb(0, 0, 0), #031723);'
  }
})

header.addEventListener('mouseenter', () => {
  if (window.scrollY > 50){
     header.style = `background: linear-gradient(to bottom, rgb(0, 0, 0), #031723); border-bottom: 2px orange solid; filter:opacity(1); transition:0.5s filter ease-in-out;`
  }
})

header.addEventListener('mouseleave', () => {
  if(window.scrollY > 50){
    header.style = `filter:opacity(0); transition:0.5s filter ease-in-out;`
  }
  
})
}else{
  header.style = 'background: linear-gradient(to bottom, rgb(0, 0, 0), #031723);'
}

document.addEventListener('DOMContentLoaded', () => {
  carregarConfiguracoes();
});

function carregarConfiguracoes() {
  fetch('admin/api/api_configuracoes.php')
    .then(response => response.json())
    .then(config => {
      configBanco = config;
       let diasFuncionamento = config.dias_funcionamento;


            // Caso venha como JSON em formato de string
            if (typeof diasFuncionamento === "string") {

                try {

                    diasFuncionamento =
                        JSON.parse(diasFuncionamento);

                } catch (erro) {

                    console.error(
                        "Erro ao interpretar dias_funcionamento:",
                        erro
                    );

                    diasFuncionamento = {};

                }

            }


            // Garante que seja um objeto
            if (
                !diasFuncionamento ||
                typeof diasFuncionamento !== "object" ||
                Array.isArray(diasFuncionamento)
            ) {

                diasFuncionamento = {};

            }


            // Guarda o objeto convertido
            configBanco.dias_funcionamento =
                diasFuncionamento;
      atualizarTextosSobre(config);
    })
    .catch(erro => console.error('Erro ao carregar configurações na página Sobre:', erro));
}

function atualizarTextosSobre(config) {

  
 const elemHorarioFooter =
        document.querySelector(
            ".funcionamento p:nth-child(2)"
        );


    if (elemHorarioFooter) {

        const nomesDias = [
            "Domingo",
            "Segunda-feira",
            "Terça-feira",
            "Quarta-feira",
            "Quinta-feira",
            "Sexta-feira",
            "Sábado"
        ];


        const dias =
            configBanco.dias_funcionamento || {};


        const textosDias = [];


        Object.keys(dias).forEach((dia) => {

            const horario =
                dias[dia];


            if (
                horario &&
                horario.abertura &&
                horario.fechamento
            ) {

                textosDias.push(
                    `${nomesDias[Number(dia)]}: ${horario.abertura.substring(0, 5)}h às ${horario.fechamento.substring(0, 5)}h`
                );

            }

        });


        if (textosDias.length > 0) {

            elemHorarioFooter.innerHTML =
                `<strong>Restaurante:</strong> ${textosDias.join(" | ")}.`;

        } 
  }
}