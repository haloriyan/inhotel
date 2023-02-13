const Element = (tag, attributes) => {
    let el = document.createElement(tag);
    
    render = (target, content) => {
        el.innerHTML = content;
        for (let key in attributes) {
            el.setAttribute(key, attributes[key]);
        }
        let renderTo = select(target);
        renderTo.appendChild(el);
    }

    return this;
}

// Element("h2", {id: 'tes'}).render("#renderArea", "<p>Cok</p>");