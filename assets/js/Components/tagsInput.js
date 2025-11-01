class TagsInput {
    
    constructor({ inputId, containerId, hiddenId }) {
        this.input = document.getElementById(inputId);
        this.container = document.getElementById(containerId);
        this.hidden = document.getElementById(hiddenId);
        this.tags = [];

        if (!this.input || !this.container || !this.hidden) {
            console.warn(`TagsInput: élément manquant (${inputId}, ${containerId}, ${hiddenId})`);
            return;
        }

        console.log(`TagsInput initialisé pour ${inputId}`);
        this.init();
    }

    init() {
        // Charger les anciennes valeurs (si présentes)
        const oldValue = this.hidden.value.trim();
        if (oldValue !== '') {
            oldValue.split(';').forEach(tag => this.addTag(tag.trim()));
        }

        // Écoute des touches
        this.input.addEventListener('keydown', (e) => {
            if (e.key === ';' || e.key === 'Enter') {
                e.preventDefault();
                this.addTag(this.input.value);
            } else if (e.key === 'Backspace' && this.input.value === '') {
                this.removeLastTag();
            }
        });

        // Écoute de la suppression au clic
        this.container.addEventListener('click', (e) => {
            if (e.target.classList.contains('btn-close')) {
                const value = e.target.getAttribute('data-tag');
                this.removeTag(value);
            }
        });
    }

    addTag(text) {
        text = text.trim();
        if (text !== '' && !this.tags.includes(text)) {
            this.tags.push(text);

            const tagEl = document.createElement('span');
            tagEl.className = 'badge rounded-pill bg-primary d-flex align-items-center';
            tagEl.innerHTML = `
                <span class="me-1">${text}</span>
                <button type="button" class="btn-close btn-close-white btn-sm ms-1" 
                        aria-label="Remove" data-tag="${text}" style="font-size: 0.6rem;"></button>
            `;
            this.container.appendChild(tagEl);
            this.updateHidden();
        }
        this.input.value = '';
    }

    removeTag(value) {
        this.tags = this.tags.filter(tag => tag !== value);
        const badge = this.container.querySelector(`[data-tag="${value}"]`)?.closest('.badge');
        badge?.remove();
        this.updateHidden();
    }

    removeLastTag() {
        this.tags.pop();
        this.container.lastChild?.remove();
        this.updateHidden();
    }

    updateHidden() {
        this.hidden.value = this.tags.join(';');
    }

    getValue() {
        return this.hidden.value;
    }
}

export default TagsInput;
