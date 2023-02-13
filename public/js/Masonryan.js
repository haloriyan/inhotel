class Masonryan {
    constructor(props) {
        this.dividedBy = props.dividedBy;
        this.items = document.querySelectorAll(props.items);
        this.totalItems = this.items.length;
        this.mod = this.totalItems % this.dividedBy;
        this.squaredItem = this.totalItems - this.mod;
        this.itemPerRow = this.squaredItem / this.dividedBy;
        this.container = props.container;
		this.dividenWidth = 100 / this.dividedBy;

        this.k = 0;
        for (let i = 0; i < this.dividedBy; i++) {
            this.createElement({
                el: 'div',
                attributes: [
                    ['class', `row-${i}`],
					['style', `display: inline-block;margin: 0px;width: ${this.dividenWidth}%;vertical-align: top;`]
                ],
                createTo: this.container
            });

            for (let j = 0; j < this.itemPerRow; j++) {
                let htmlContent = this.items[i].innerHTML;
                this.createElement({
                    el: 'div',
                    attributes: [['style', 'margin: 0px;']],
                    html: this.items[this.k].innerHTML,
                    createTo: `${this.container} .row-${i}`
                });
                this.k += 1;
            }
        }

        // Render sisa
        if (this.mod != 0) {
            for (let i = 0; i < this.mod; i++) {
                let htmlContent = this.items[this.k].innerHTML;
                this.createElement({
                    el: 'div',
                    attributes: [['style', 'margin: 0px;']],
                    html: htmlContent,
                    createTo: `${this.container} .row-${i}`
                });
                this.k += 1;
            }
        }

        this.items.forEach(item => item.remove());
    }
	createElement(props) {
		document.querySelectorAll(props.createTo).forEach(target => {
			let el = document.createElement(props.el)
			if (props.attributes !== undefined) {
				props.attributes.forEach(res => {
					el.setAttribute(res[0], res[1])
				});
			}
			if(props.html !== undefined) {
				el.innerHTML = props.html;
			}
			target.appendChild(el);
		});
	}
}