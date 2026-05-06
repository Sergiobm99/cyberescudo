
    // Lógica del minijuego
    document.addEventListener('DOMContentLoaded', () => {
        let foundCount = 0;
        const totalFlags = 3;
        const counterElement = document.getElementById('flags-counter');
        const successMessage = document.getElementById('success-message');
        const suspiciousElements = document.querySelectorAll('.suspicious-element');

        suspiciousElements.forEach(element => {
            element.addEventListener('click', function(e) {
                // Evitar que el link haga scroll hacia arriba
                e.preventDefault();

                // Si no ha sido encontrado ya
                if (!this.classList.contains('found')) {
                    this.classList.add('found');
                    foundCount++;
                    counterElement.innerText = foundCount;

                    // Mostrar tooltips o explicaciones visuales opcionales
                    if (this.getAttribute('data-info')) {
                        let infoSpan = document.createElement('span');
                        infoSpan.style.display = 'block';
                        infoSpan.style.fontSize = '0.8rem';
                        infoSpan.style.color = '#ff2a2a';
                        infoSpan.style.marginTop = '5px';
                        infoSpan.innerText = "🚩 " + this.getAttribute('data-info');
                        this.parentNode.insertBefore(infoSpan, this.nextSibling);
                    }

                    // Condición de victoria
                    if (foundCount === totalFlags) {
                        setTimeout(() => {
                            successMessage.style.display = 'block';
                            // Scroll suave hacia el mensaje de éxito
                            successMessage.scrollIntoView({ behavior: 'smooth' });
                        }, 500);
                    }
                }
            });
        });
    });
