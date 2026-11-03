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
