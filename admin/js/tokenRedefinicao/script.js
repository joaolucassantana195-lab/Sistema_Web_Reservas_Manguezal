//tratamento de erro formulario
let form = document.querySelector('form')
let span = document.querySelector('.spn-erro')

form.addEventListener('submit', (e) => {
    e.preventDefault();
    const submitBtn = e.submitter;
    
    submitBtn.textContent = "Enviando...";
    submitBtn.disabled = true;
    
    const iptEmail = document.querySelector('#email').value;
       
    fetch('../admin/api/api_enviar_senha_token_admin.php', {
            method: 'POST', 
            headers: {
                'Content-Type' : 'application/json'
            },
            body: JSON.stringify({
                email : iptEmail
            })
        }).then(response => response.json()).then(data =>{
            if (data.sucesso){
                window.location.href = 'index.php';
                span.textContent = data.mensagem
                submitBtn.textContent = "Enviar token";
                submitBtn.disabled = false;
            }else{
                span.textContent = data.mensagem
                submitBtn.textContent = "Enviar token";
                submitBtn.disabled = false;
            }
        }).catch(error => {
            submitBtn.textContent = "Enviar token";
            submitBtn.disabled = false;
    
            console.error('Erro na requisição');
        });
})

function reenviarToken(e) {
    if (e && typeof e.preventDefault === 'function') {
        e.preventDefault();
    }

    const btnReenviar = e ? e.target : document.querySelector('#btn-reenviar');
    const iptEmail = document.querySelector('#email').value;

    if (!iptEmail) {
        span.textContent = "Por favor, preencha o e-mail antes de reenviar o token.";
        return;
    }

    fetch('../admin/api/api_enviar_senha_token_admin.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json'
        },
        body: JSON.stringify({
            email: iptEmail
        })
    })
    .then(response => response.json())
    .then(data => {
        span.textContent = data.mensagem;
        
        if (data.sucesso) {
            iniciarCooldown(btnReenviar);
            window.location.href = 'index.php';
        }
    })
    .catch(error => {
        span.textContent = "Erro de conexão ao tentar reenviar o token.";
        console.error('Erro na requisição de reenvio:', error);
    });
}

function iniciarCooldown(btn) {
    let segundosRestantes = 60;
    const textoOriginal = "Reenviar token";

    btn.disabled = true;
    btn.textContent = `${textoOriginal} (${segundosRestantes}s)`;

    const timer = setInterval(() => {
        segundosRestantes--;

        if (segundosRestantes > 0) {
            btn.textContent = `${textoOriginal} (${segundosRestantes}s)`;
        } else {
            clearInterval(timer);
            btn.disabled = false;
            btn.textContent = textoOriginal;
        }
    }, 1000);
}
