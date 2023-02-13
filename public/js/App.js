let user = JSON.parse(select("#user").value);

const ActivateHeader = () => {
    console.log('cok');
    select(".TopBar").classList.add('active');
    select(".TopBar").style.background = user.accent_color;
    select(".TopBar form").style.background = pSBC(-0.4, user.accent_color);
    select(".TopBar form").style.border = 'none';
}

window.addEventListener('scroll', e => {
    let scroll = window.scrollY;
    if (screen.width > 480) {
        if (scroll > 200) {
            ActivateHeader();
        } else {
            if (forceHeaderActive == 0) {
                select(".TopBar").classList.remove('active');
                select(".TopBar").style.background = 'none';
                select(".TopBar form").style.background = 'none';
                select(".TopBar form").style.border = '1px solid #fff';
            }
        }
    }
});

if (screen.width < 480) {
    ActivateHeader();
}

if (forceHeaderActive == 1) {
    ActivateHeader();
}

let visitor = JSON.parse(localStorage.getItem('visitor'));
if (visitor == null) {
    modal("#LoginVisitor").show();
}

const loginVisitor = (e) => {
    let name = select("#LoginVisitor input#name");
    let email = select("#LoginVisitor input#email");
    let phone = select("#LoginVisitor input#phone");

    post("/api/visitor/login", {
        name: name.value,
        email: email.value,
        phone: phone.value,
        user_id: user.id
    })
    .then(res => {
        console.log(res);
        localStorage.setItem('visitor', JSON.stringify(res.visitor));
        modal("#LoginVisitor").hide();
    });

    e.preventDefault();
}