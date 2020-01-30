
const addBusiness = async () => {

    try {

        const formData = new FormData(document.querySelector("#businessForm"));

        formData.append('class', 'Business');
        formData.append('method', 'createBusiness');

        formData.forEach(value => {
            console.log(value);
        });
        
        await fetch('http://localhost/wsfia-dev/configuration/api.php'
            , { method: 'POST'
            , body: formData
            }
        )
        .then(response => response.json())
        .then(json => {
            if (json) {
                window.location.replace("/register");
            }
        })
        .catch(error => displayError(error));
        
        
    }
    catch {
        console.error('catch error');
    }

    return false;
    
}