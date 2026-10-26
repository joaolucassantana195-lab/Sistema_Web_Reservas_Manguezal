//tratamento de erro formulario
let form = document.querySelector('form')
let span = document.querySelector('span')

form.addEventListener('submit', (e) => {
    e.preventDefault();
    const submitBtn = e.submitter;
    
    submitBtn.textContent = "Entrando...";
    submitBtn.disabled = true;
    
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
                submitBtn.textContent = "Efetuar Login";
                submitBtn.disabled = false;
            }
        }).catch(error => {
            submitBtn.textContent = "Efetuar Login";
            submitBtn.disabled = false;
    
            console.error('Erro na requisição');
        });
})
