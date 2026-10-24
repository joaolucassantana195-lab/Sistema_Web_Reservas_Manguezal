const body = document.querySelector("body");

const formulario = document.querySelector("#formularioReserva");

const btnConfirmar = document.querySelector("#btn-confirmar");

const containerHorarios = document.querySelector("#container-horarios");

const nomeCliente = document.querySelector("#nome");

const emailCliente = document.querySelector("#email");

const telefoneCliente = document.querySelector("#telefone");

const data = document.querySelector("#data");

const inputHorario = document.querySelector("#horario");

const qtdPessoas = document.querySelector("#pessoas");

const reservaBeach = document.querySelector("#optionBeach");

let spnnome = document.querySelector("#nome-erro");
let spnemail = document.querySelector("#email-erro");
let spnpessoas = document.querySelector("#pessoas-erro");
let spntelefone = document.querySelector("#telefone-erro");
let spndata = document.querySelector("#data-erro");
let spnhorario = document.querySelector("#horario-erro");
let spnbeach = document.querySelector("#beach-erro");

let configBanco = null;

const horariosFuncionamento = [];

let dataMax = null;


// ============================================================
// CARREGAR CONFIGURAÇÕES
// ============================================================

function carregarConfiguracoes() {

    fetch("admin/api/api_configuracoes.php")

        .then((response) => {

            if (!response.ok) {
                throw new Error(
                    "Erro HTTP: " + response.status
                );
            }

            return response.json();

        })

        .then((config) => {

            configBanco = config;


            // ========================================================
            // CONVERTER DIAS DE FUNCIONAMENTO
            // ========================================================

            let diasFuncionamento = config.dias_funcionamento;


            // Caso venha como JSON em formato de string
            if (typeof diasFuncionamento === "string") {

                try {

                    diasFuncionamento =
                        JSON.parse(diasFuncionamento);

                } catch (erro) {

                    console.error(
                        "Erro ao interpretar dias_funcionamento"
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



            // ========================================================
            // DATA ATUAL
            // ========================================================

            const agora = new Date();


            // ========================================================
            // HORÁRIO LIMITE DA ÚLTIMA RESERVA
            // ========================================================

            const limiteStr =
                config.horario_limite_ultima_reserva ||
                "16:00";


            const [
                horasLimite,
                minutosLimite
            ] = limiteStr
                .split(":")
                .map(Number);


            const dataLimiteHoje =
                new Date(agora);


            dataLimiteHoje.setHours(
                horasLimite,
                minutosLimite,
                0,
                0
            );


            // ========================================================
            // DATA MÍNIMA
            // ========================================================

            const dataMinima =
                new Date(agora);


            if (agora >= dataLimiteHoje) {

                dataMinima.setDate(
                    dataMinima.getDate() + 1
                );

            }


            // ========================================================
            // DATA MÁXIMA
            // ========================================================

            const diasAntecedenciaMax =
                parseInt(
                    config.antecedencia_max_reserva_dias,
                    10
                ) || 30;


            const dataMaxima =
                new Date(agora);


            dataMaxima.setDate(
                dataMaxima.getDate() +
                diasAntecedenciaMax
            );


            dataMax = dataMaxima;


            // ========================================================
            // CONFIGURAR CALENDÁRIO
            // ========================================================

            flatpickr(data, {

                dateFormat: "d/m/Y",

                minDate: dataMinima,

                maxDate: dataMaxima,

                disableMobile: true,

                enable: [

                    function (date) {

                        const diaSemana =
                            date.getDay();

                        /*
                         * Domingo = 0
                         * Segunda = 1
                         * Terça   = 2
                         * Quarta  = 3
                         * Quinta  = 4
                         * Sexta   = 5
                         * Sábado  = 6
                         */

                        return Boolean(
                            configBanco
                                .dias_funcionamento[
                                    String(diaSemana)
                                ]
                        );

                    }

                ]

            });


            // ========================================================
            // LIMITE DE PESSOAS
            // ========================================================

            qtdPessoas.placeholder =
                `Mínimo de ${config.min_pessoas_reserva} e máximo de ${config.max_pessoas_reserva} pessoas`;


            // ========================================================
            // ATUALIZAR TEXTOS
            // ========================================================

            atualizarTextosInterface(config);

        })

        .catch((erro) => {

            console.error(
                "Erro ao carregar configurações"
            );

        });

}


// ============================================================
// FORMATAR DATA YYYY-MM-DD
// ============================================================

function formatarDataYYYYMMDD(data) {

    const ano =
        data.getFullYear();

    const mes =
        String(
            data.getMonth() + 1
        ).padStart(2, "0");

    const dia =
        String(
            data.getDate()
        ).padStart(2, "0");

    return `${ano}-${mes}-${dia}`;

}


// ============================================================
// GERAR HORÁRIOS DE ACORDO COM A DATA
// ============================================================

function gerarHorariosDisponiveis(dataSelecionada) {

    horariosFuncionamento.length = 0;


    if (!dataSelecionada) {
        return;
    }


    // ========================================================
    // DESCOBRIR DIA DA SEMANA
    // ========================================================

    const diaSemana =
        dataSelecionada.getDay();




    // ========================================================
    // PEGAR CONFIGURAÇÃO DO DIA
    // ========================================================

    const diasFuncionamento =
        configBanco?.dias_funcionamento || {};


    const horarioDia =
        diasFuncionamento[
            String(diaSemana)
        ];




    // ========================================================
    // VERIFICAR SE O DIA FUNCIONA
    // ========================================================

    if (
        !horarioDia ||
        !horarioDia.abertura ||
        !horarioDia.fechamento
    ) {

        return;

    }


    // ========================================================
    // HORÁRIO DE ABERTURA
    // ========================================================

    const [
        horaAbertura,
        minutoAbertura
    ] =
        horarioDia.abertura
            .substring(0, 5)
            .split(":")
            .map(Number);


    // ========================================================
    // HORÁRIO DE FECHAMENTO
    // ========================================================

    const [
        horaFechamento,
        minutoFechamento
    ] =
        horarioDia.fechamento
            .substring(0, 5)
            .split(":")
            .map(Number);


    const inicioEmMinutos =
        horaAbertura * 60 +
        minutoAbertura;


    const fechamentoEmMinutos =
        horaFechamento * 60 +
        minutoFechamento;


    // ========================================================
    // LIMITE DA ÚLTIMA RESERVA
    // ========================================================

    let limiteReservaEmMinutos =
        fechamentoEmMinutos;


    if (
        configBanco.horario_limite_ultima_reserva
    ) {

        const [
            horaLimite,
            minutoLimite
        ] =
            configBanco
                .horario_limite_ultima_reserva
                .substring(0, 5)
                .split(":")
                .map(Number);


        const limiteConfiguracao =
            horaLimite * 60 +
            minutoLimite;


        /*
         * O último horário disponível será
         * o menor entre:
         *
         * - fechamento do dia
         * - limite da última reserva
         */

        limiteReservaEmMinutos =
            Math.min(
                fechamentoEmMinutos,
                limiteConfiguracao
            );

    }


    // ========================================================
    // GERAR HORÁRIOS DE 15 EM 15 MINUTOS
    // ========================================================

    for (
        let minutos = inicioEmMinutos;
        minutos <= limiteReservaEmMinutos;
        minutos += 15
    ) {

        const hora =
            Math.floor(
                minutos / 60
            );


        const minuto =
            minutos % 60;


        const horaFormatada =
            `${String(hora).padStart(2, "0")}:${String(minuto).padStart(2, "0")}`;


        horariosFuncionamento.push(
            horaFormatada
        );

    }


}


// ============================================================
// ATUALIZAR TEXTOS DA INTERFACE
// ============================================================

function atualizarTextosInterface(config) {

    const elemTolerancia =
        document.querySelector(
            ".lista-regras li:nth-child(1) p"
        );


    if (elemTolerancia) {

        elemTolerancia.textContent =
            `Mesa garantida por até ${config.tolerancia_atraso_minutos} minutos após o horário agendado.`;

    }


    const elemBeach =
        document.querySelector(
            ".lista-regras li:nth-child(6) p"
        );


    if (elemBeach) {

        elemBeach.textContent =
            `Cobramos presencialmente uma taxa de R$ ${parseFloat(
                config.taxa_pessoa_beach_club
            )
                .toFixed(2)
                .replace(".", ",")} por pessoa para utilização da infraestrutura da área restrita.`;

    }


    const elemFaqReserva =
        document.querySelector(
            ".faq-lista .faq-item:nth-child(1) .faq-resposta p"
        );


    if (elemFaqReserva) {

        elemFaqReserva.innerHTML =
            `Para mesas individuais e pequenas famílias, o atendimento é por <strong>ordem de chegada</strong>. As reservas pelo site são exclusivas para <strong>grupos e excursões</strong> a partir de ${config.min_pessoas_reserva} pessoas.`;

    }


    // ========================================================
    // HORÁRIOS NO FOOTER
    // ========================================================

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


    // ========================================================
    // BEACH CLUB
    // ========================================================

    const campoBeach =
        document.querySelector(
            ".campo-beach"
        );


    if (campoBeach) {

        const permiteBeach =
            Number(
                config.permitir_reserva_beach_club
            ) === 1;


        if (!permiteBeach) {

            campoBeach.innerHTML = `
                <label>Reserva para Beach Club?</label>

                <p
                    style="
                        text-align: start;
                        font-size: 1rem;
                        margin: 10px;
                        margin-top: 20px;
                        font-weight: bold;
                        color: orange;
                    "
                >
                    Beach Club indisponível no momento
                </p>
            `;

        } else {

            campoBeach.innerHTML = `
                <label>Reserva para Beach Club?</label>

                <select
                    id="optionBeach"
                    name="optionBeach"
                >

                    <option value="nao">
                        Não
                    </option>

                    <option value="sim">
                        Sim
                    </option>

                </select>

                <span
                    id="beach-erro"
                    style="color: red;"
                ></span>
            `;

        }

    }

}


// ============================================================
// QUANDO A DATA FOR ALTERADA
// ============================================================

data.addEventListener("change", function () {

    // Limpa horários antigos
    containerHorarios.innerHTML = "";

    // Limpa horário selecionado
    inputHorario.value = "";


    // ========================================================
    // VERIFICAR DATA
    // ========================================================

    if (!this.value) {

        containerHorarios.innerHTML =
            "<p>Insira uma data primeiro</p>";

        return;

    }


    // ========================================================
    // CONVERTER DATA DD/MM/YYYY
    // ========================================================

    const partesData =
        this.value.split("/");


    if (partesData.length !== 3) {

        containerHorarios.innerHTML =
            "<p>Data inválida.</p>";

        return;

    }


    const dia =
        Number(partesData[0]);


    const mes =
        Number(partesData[1]) - 1;


    const ano =
        Number(partesData[2]);


    const dataSelecionada =
        new Date(
            ano,
            mes,
            dia
        );


    // ========================================================
    // GERAR HORÁRIOS DO DIA
    // ========================================================

    gerarHorariosDisponiveis(
        dataSelecionada
    );


    // ========================================================
    // VERIFICAR SE EXISTEM HORÁRIOS
    // ========================================================

    if (
        horariosFuncionamento.length === 0
    ) {

        containerHorarios.innerHTML =
            "<p>Nenhum horário disponível para esta data.</p>";

        return;

    }


    // ========================================================
    // CRIAR BOTÕES
    // ========================================================

    horariosFuncionamento.forEach(
        (hora) => {

            const btnHora =
                document.createElement(
                    "button"
                );


            btnHora.type =
                "button";


            btnHora.textContent =
                hora;


            btnHora.classList.add(
                "btn-horario"
            );


            btnHora.addEventListener(
                "click",
                function (e) {

                    e.preventDefault();


                    // Remove seleção dos outros
                    document
                        .querySelectorAll(
                            ".btn-horario"
                        )
                        .forEach((btn) => {

                            btn.classList.remove(
                                "selecionado"
                            );

                        });


                    // Seleciona o botão atual
                    this.classList.add(
                        "selecionado"
                    );


                    // Salva horário
                    inputHorario.value =
                        hora;


                    // Remove mensagem de erro
                    spnhorario.innerText =
                        "";

                }
            );


            containerHorarios.appendChild(
                btnHora
            );

        }
    );

});


// ============================================================
// VALIDAR NOME
// ============================================================

function validarNome(nome) {

    if (
        !nome ||
        nome.trim() === ""
    ) {

        spnnome.innerText =
            "Insira um nome válido!";

        return false;

    }


    if (
        nome
            .replace(/\s/g, "")
            .length < 8
    ) {

        spnnome.innerText =
            "O nome precisa conter pelo menos 8 caracteres.";

        return false;

    }


    const regexNome =
        /^[A-Za-zÀ-ÖØ-öø-ÿ]+(\s+[A-Za-zÀ-ÖØ-öø-ÿ]+)+$/;


    if (
        !regexNome.test(
            nome.trim()
        )
    ) {

        spnnome.innerText =
            "Caracteres especiais não são permitidos.";

        return false;

    }


    spnnome.innerText = "";

    return true;

}


// ============================================================
// VALIDAR EMAIL
// ============================================================

function validarEmail(email) {

    if (
        !email ||
        email.trim() === ""
    ) {

        spnemail.innerText =
            "Insira um e-mail válido!";

        return false;

    }


    const regexEmail =
        /^[a-zA-Z0-9._%+-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,}$/;


    if (
        !regexEmail.test(
            email.trim()
        )
    ) {

        spnemail.innerText =
            "Insira um e-mail válido!";

        return false;

    }


    spnemail.innerText = "";

    return true;

}


// ============================================================
// VALIDAR TELEFONE
// ============================================================

function validarTelefone(numero) {

    if (
        !numero ||
        numero.trim() === ""
    ) {

        spntelefone.innerText =
            "Este campo é obrigatório!";

        return false;

    }


    const regexTelefone =
        /^(?:\+?55\s?)?(?:\(?\d{2}\)?\s?)?9\d{4}[-\s]?\d{4}$/;


    if (
        !regexTelefone.test(
            numero.trim()
        )
    ) {

        spntelefone.innerText =
            "Telefone inválido!";

        return false;

    }


    spntelefone.innerText = "";

    return true;

}


// ============================================================
// VALIDAR DATA
// ============================================================

function validarData(date) {

    if (
        !date ||
        date.trim() === ""
    ) {

        spndata.innerText =
            "Este campo é obrigatório!";

        return false;

    }


    spndata.innerText = "";

    return true;

}


// ============================================================
// VALIDAR HORÁRIO
// ============================================================

function validarHorario(hora) {

    if (
        !hora ||
        hora.trim() === ""
    ) {

        spnhorario.innerText =
            "Selecione um horário disponível!";

        return false;

    }


    if (configBanco) {

        const antecedenciaMinHoras =
            parseInt(
                configBanco
                    .antecedencia_min_reserva_horas,
                10
            ) || 0;


        const partesData =
            data.value.split("/");


        if (
            partesData.length === 3
        ) {

            const dataSelecionada =
                new Date(
                    partesData[2],
                    partesData[1] - 1,
                    partesData[0]
                );


            const hoje =
                new Date();


            const ehHoje =
                dataSelecionada.getDate() ===
                    hoje.getDate() &&

                dataSelecionada.getMonth() ===
                    hoje.getMonth() &&

                dataSelecionada.getFullYear() ===
                    hoje.getFullYear();


            if (ehHoje) {

                const agoraMaisAntecedencia =
                    new Date();


                agoraMaisAntecedencia.setHours(
                    agoraMaisAntecedencia.getHours() +
                    antecedenciaMinHoras
                );


                const [
                    horasSel,
                    minutosSel
                ] =
                    hora
                        .split(":")
                        .map(Number);


                const dataHorarioSelecionado =
                    new Date();


                dataHorarioSelecionado.setHours(
                    horasSel,
                    minutosSel,
                    0,
                    0
                );


                if (
                    dataHorarioSelecionado <
                    agoraMaisAntecedencia
                ) {

                    spnhorario.innerText =
                        `Reservas para hoje exigem antecedência mínima de ${antecedenciaMinHoras}h.`;

                    return false;

                }

            }

        }

    }


    spnhorario.innerText = "";

    return true;

}


// ============================================================
// VALIDAR PESSOAS
// ============================================================

function validarPessoas(pessoasInput) {

    const valor =
        pessoasInput.value
            ? pessoasInput.value.trim()
            : "";


    if (!valor) {

        spnpessoas.innerText =
            "Este campo é obrigatório!";

        return false;

    }


    const numPessoas =
        parseInt(
            valor,
            10
        );


    const min =
        configBanco
            ? parseInt(
                configBanco.min_pessoas_reserva
            )
            : 30;


    const max =
        configBanco
            ? parseInt(
                configBanco.max_pessoas_reserva
            )
            : 50;


    if (isNaN(numPessoas)) {

        spnpessoas.innerText =
            "Insira um número válido!";

        return false;

    }


    if (numPessoas < min) {

        spnpessoas.innerText =
            `Mínimo de ${min} pessoas para reservar.`;

        return false;

    }


    if (numPessoas > max) {

        spnpessoas.innerText =
            `Máximo de ${max} pessoas para reservar.`;

        return false;

    }


    spnpessoas.innerText = "";

    return true;

}


// ============================================================
// VALIDAR BEACH CLUB
// ============================================================

function validarBeach(option) {

    if (
        configBanco &&
        Number(
            configBanco.permitir_reserva_beach_club
        ) === 0
    ) {

        spnbeach.innerText = "";

        return true;

    }


    if (
        !option ||
        option.trim() === ""
    ) {

        spnbeach.innerText =
            "Este campo é obrigatório!";

        return false;

    }


    spnbeach.innerText = "";

    return true;

}


// ============================================================
// VALIDAR FORMULÁRIO
// ============================================================

function credenciaisValidas() {

    let valido = true;


    if (
        !validarNome(
            nomeCliente.value
        )
    ) {

        valido = false;

    }


    if (
        !validarEmail(
            emailCliente.value
        )
    ) {

        valido = false;

    }


    if (
        !validarTelefone(
            telefoneCliente.value
        )
    ) {

        valido = false;

    }


    if (
        !validarData(
            data.value
        )
    ) {

        valido = false;

    }


    if (
        !validarHorario(
            inputHorario.value
        )
    ) {

        valido = false;

    }


    if (
        !validarPessoas(
            qtdPessoas
        )
    ) {

        valido = false;

    }


    if (
        reservaBeach &&
        !validarBeach(
            reservaBeach.value
        )
    ) {

        valido = false;

    }


    return valido;

}


// ============================================================
// ENVIO DO FORMULÁRIO
// ============================================================

formulario.addEventListener(
    "submit",
    (event) => {

        event.preventDefault();


        if (!credenciaisValidas()) {

            return;

        }


        btnConfirmar.disabled = true;


        const modal =
            document.createElement(
                "div"
            );


        modal.style = `
            display: flex;
            align-items: center;
            position: fixed;
            top: 0;
            left: 0;
            width: 100vw;
            height: 100vh;
            background-color: rgba(0, 0, 0, 0.30);
            justify-content: center;
            z-index: 9999;
            font-size: 20px;
        `;


        modal.innerHTML = `
            <div
                class="varela-round-regular"
                style="
                    background: linear-gradient(
                        145deg,
                        var(--rm-deep),
                        #0c6870
                    );
                    color: white;
                    padding: 20px;
                    margin: 20px;
                    border-radius: 15px;
                    border: 2px solid #094a50;
                    font-weight: 400;
                    box-shadow: 0px 10px 15px #00000036;
                "
            >

                <p>
                    Confirma que os dados
                    informados estão corretos?
                </p>

                <div
                    class="divnaocomparecu-acoes"
                    style="
                        width: 100%;
                        display: flex;
                        justify-content: end;
                        gap: 10px;
                    "
                >

                    <button
                        type="button"
                        class="btn-confirmar"
                    >
                        Sim, confirmar
                    </button>

                    <button
                        type="button"
                        class="btn-cancelar"
                    >
                        Não, corrigir
                    </button>

                </div>

            </div>
        `;


        body.appendChild(modal);


        // ========================================================
        // CONFIRMAR
        // ========================================================

        modal
            .querySelector(".btn-confirmar")
            .addEventListener(
                "click",
                () => {

                    btnConfirmar.innerText =
                        "Enviando...";

                    setInterval(()=>{
                        btnConfirmar.disabled = false;
                        btnConfirmar.innerText = "Garantir Minha Reserva";
                    },5000)    


                    body.removeChild(
                        modal
                    );


                    formulario.submit();

                }
            );


        // ========================================================
        // CANCELAR
        // ========================================================

        modal
            .querySelector(".btn-cancelar")
            .addEventListener(
                "click",
                () => {

                    btnConfirmar.disabled =
                        false;


                    btnConfirmar.innerText =
                        "Garantir Minha Reserva";


                    body.removeChild(
                        modal
                    );

                }
            );

    }
);


// ============================================================
// INICIALIZAÇÃO
// ============================================================

carregarConfiguracoes();