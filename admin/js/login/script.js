//tratamento de erro formulario
let form = document.querySelector('form')
let span = document.querySelector('span')

form.addEventListener('submit', (e) => {
    e.preventDefault();
    e.target.textContent = "Entrando..."
    e.target.disabled = true
    const iptEmail = document.querySelector('#email').value;
    const iptSenha = document.querySelector('#senha').value;
       
    fetch('../admin/api/api_login.php', {
            method: 'POST', 
            headers: {
                'Content-Type' : 'application/json'
            },
            body: JSON.stringify({
                email : iptEmail,
                senha : iptSenha
            })
        }).then(response => response.json()).then(data =>{
            if (data.sucesso){
                window.location.href = data.redirect
                span.textContent = data.mensagem
            }else{
                span.textContent = data.mensagem
                e.target.textContent = "Efetuar Login"
                e.target.disabled = false
            }
        }).catch(error => {
            e.target.textContent = "Efetuar Login"
            e.target.disabled = false
            console.error('Erro na requisição');
        });
})
