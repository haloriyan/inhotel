let state = {
    ableToResendOtp: true,
    selected_event_types: [],
    myData: JSON.parse(localStorage.getItem('user_data')),
    google_action: 'login',
    currentScreen: 0,
    execution_type_description: {
        offline: "Deskripsi kalau event offline",
        hybrid: "Deskripsi kalau event hybrid",
        online: "Deskripsi kalau event online",
    },
    screenWidth: screen.width,
    
    ticket_price: "0",
    ticket_quantity: 1,

    provinces: [],
    cities: [],
    organizers: [],

    // form field
    field: {
        organizer_id: null,
        event_name: "",
        event_description: "",
        breakdowns: [],
        execution_type: 'offline',
        province: "",
        city: "",
        tickets: [],
        topics: [],
    },
};

if (state.myData == "" || state.myData == null) {
    modal("#modalLogin").show();
} else {
    if (state.myData.is_active == 0) {
        modal("#modalOtp").show();
        select("#modalOtp #email").innerText = state.myData.email;
    }
}

let screens = [
    {
        id: "tipeEvent",
        footer: ["Beda Tipe Beda Kebutuhan", "Kami membantumu menyesuaikan apa yang eventmu butuhkan"],
        callback: () => {
            writeFooter('Beda Tipe Beda Kebutuhan', 'Kami membantumu menyesuaikan apa yang eventmu butuhkan');
        },
        validation: () => {
            if (state.selected_event_types.length == 0) {
                return writeError("Kamu harus memilih setidaknya satu tipe event")
            }
            return true;
        }
    },
    {
        id: "basicInfo",
        callback: () => {
            writeFooter('Ada Apa di Eventmu?', 'Berikan informasi tentang eventmu dengan jelas');
        },
        validation: () => {
            return true;
            if (state.field.event_name == "") {
                return writeError('Masa eventmu ga ada judulnya?');
            }
            if (state.field.event_description == "") {
                return writeError('Paling tidak jelasin eventmu akan membahas apa pada deskripsi event');
            }
            return true;
        }
    },
    {
        id: "ticketing",
        footer: ["Cara untuk Berpartisipasi dalam Eventmu", "Sesuaikan cara orang-orang untuk bisa hadir pada eventmu"],
        callback: () => {
            writeFooter('Cara untuk Berpartisipasi dalam Eventmu', 'Sesuaikan cara orang-orang untuk bisa hadir pada eventmu');
        },
        validation: () => {
            return true;
        }
    },
]

const typing = (type, input) => {
    state.field[type] = input.value;
}

const switchAccount = () => {
    localStorage.removeItem('user_data');
    state.myData = null;
    navigateModal('#modalLogin', this);
}

const previousScreen = () => {
    if (state.currentScreen == 0) {
        return false;
    }
    state.currentScreen--;

    let theScreen = screens[state.currentScreen];
    let screenDom = select(`.screen-item#${theScreen.id}`);
    selectAll(".screen-item").forEach(screen => {
        screen.classList.add('d-none');
        screen.classList.remove('flex');
    });
    screenDom.classList.remove('d-none');
    screenDom.classList.add('flex');
    screens[state.currentScreen].callback();
}
const nextScreen = () => {
    if (!screens[state.currentScreen].validation()) {
        return false;
    }

    if (state.currentScreen + 1 == screens.length) {
        state.currentScreen = 0;
        alert('sudah terakhir')
    } else {
        state.currentScreen++;
    }
    
    let theScreen = screens[state.currentScreen];
    let screenDom = select(`.screen-item#${theScreen.id}`);
    selectAll(".screen-item").forEach(screen => {
        screen.classList.add('d-none');
        screen.classList.remove('flex');
    });
    screenDom.classList.remove('d-none');
    screenDom.classList.add('flex');
    screens[state.currentScreen].callback();
}

const writeFooter = (title, description) => {
    select(".footer h3").innerText = title;
    select(".footer p").innerText = description;
}
const writeError = message => {
    modal("#modalError").show();
    select("#modalError #message").innerText = message;
    return false;
}
writeFooter('Beda Tipe Beda Kebutuhan', 'Kami membantumu menyesuaikan apa yang eventmu butuhkan');
const selectType = (button, key, data) => {
    data = JSON.parse(data);
    let selector = `.event-type-item[event-type='${key}']`;
    if (button.classList.contains('active')) {
        button.classList.remove('active');
        select(`${selector} img`).setAttribute('src', `images/event_types/${data.image}`);
        removeArray(data.name, state.selected_event_types);
    } else {
        button.classList.add('active');
        select(`${selector} img`).setAttribute('src', `images/event_types/${data.image_active}`);
        state.selected_event_types.push(data.name);
    }
}
const selectBreakdown = (data, button) => {
    let selector = `.breakdown-item[breakdown='${data}']`;
    if (button.classList.contains('active')) {
        button.classList.remove('active');
        removeArray(data, state.field.breakdowns);
    } else {
        button.classList.add('active');
        state.field.breakdowns.push(data);
    }
}

// handle otp input
let otpInputs = selectAll(".otp-input");
otpInputs.forEach((input, i) => {
    input.addEventListener("input", e => {
        let value = e.currentTarget.value;
        let toType = parseInt(value);
        select("#modalOtp #message").classList.add('d-none');
        select("#modalOtp #message").innerText = "...";
        if (isNaN(toType)) {
            input.value = "";
        } else {
            if (i == otpInputs.length - 1) {
                verifyOtp()
            } else {
                input.nextElementSibling.focus();
            }
        }
    });
});

const verifyOtp = () => {
    state.ableToResendOtp = false;
    select("#modalOtp #resendOtp").innerText = "verifying...";
    let code = "";
    otpInputs.forEach(input => {
        code += input.value;
    });

    post("api/user/otp", {
        code: code,
        user_id: state.myData.id
    })
    .then(res => {
        state.ableToResendOtp = false;
        let message = select("#modalOtp #message");
        message.classList.remove('d-none', 'bg-primary');
        if (res.status == 200) {
            message.classList.add('bg-green');
            modal("#modalOtp").hide(1);
            localStorage.setItem('user_data', JSON.stringify(res.user));
            state.myData = res.user;
        } else {
            message.classList.add('bg-red');
        }
        message.innerText = res.message;
        select("#modalOtp #resendOtp").innerText = "Kirim Ulang";
    })
}
const resendOtp = () => {
    if (state.ableToResendOtp) {
        // 
    }
}

const navigateModal = (destination, action = null) => {
    modal().hideAll(1);
    modal(destination).show();
    state.google_action = action;
}

const getProvinces = () => {
    let req = post("api/rajaongkir/province")
    .then(provinces => {
        state.provinces = provinces;
        provinces.forEach(province => {
            Element("option", {
                value: province.province
            })
            .render("#modalLocation #province", province.province);
        })
    })
}
const getCities = (provinceID) => {
    let req = post("api/rajaongkir/city", {
        province_id: provinceID
    })
    .then(cities => {
        state.cities = cities;
        select("#modalLocation #city").innerHTML = '';
        state.field.city = cities[0].city_name;
        cities.forEach(city => {
            Element("option", {
                value: city.city_name
            })
            .render("#modalLocation #city", city.city_name);
        })
    })
}
const chooseProvince = province => {
    state.provinces.forEach(prov => {
        if (province == prov.province) {
            getCities(prov.province_id);
        }
    });
    state.field.province = province;
}
const chooseCity = city => {
    state.field.city = city;
}
getProvinces();

const submitLocation = e => {
    select("#location_display").innerHTML = `${state.field.city}, ${state.field.province}`;
    modal("#modalLocation").hide();
    e.preventDefault();
}

const getOrganizers = () => {
    if (state.myData != null && state.myData != "") {
        post("api/user/organization", {
            token: state.myData.token
        })
        .then(res => {
            state.organizers = res.user.organizations;
            renderOrganizers();
        });
    }
}
const renderOrganizers = () => {
    state.organizers.forEach(organizer => {
        let organizerLogo = organizer.logo == "" ? "default_logo.png" : organizer.logo;
        Element("div", {
            class: "flex row item-center h-80 pointer",
            onclick: `chooseOrganizer('${JSON.stringify(organizer)}')`,
        })
        .render("#renderOrganizers", `
        <div class="rounded-max h-50 squarize use-height" bg-image="storage/organization_logo/${organizerLogo}"></div>
        <div class="ml-2">${organizer.name}</div>
        `);
        squarize();
        bindDivWithImage();
    })
}
getOrganizers();

const createOrganizer = (e) => {
    let name = select("#createOrganizerName");
    let formData = new FormData();
    formData.append('name', name.value);
    formData.append('token', state.myData.token);
    formData.append('logo', select("#create_organizer_logo").files[0]);
    let req = fetch("api/organization/create", {
        method: "POST",
        body: formData
    })
    .then(res => res.json())
    .then(res => {
        let organizer = res.organizer;
        state.field.organizer_id = organizer.id;
        name.value = "";
        let organizerLogo = organizer.logo;
        if (organizer.logo == undefined || organizer.logo == null) {
            organizerLogo = "default_logo.png";
        }
        select("#organizer_name_display").innerText = organizer.name;
        select("#organizer_logo_display").style.backgroundColor = "#fff";
        select("#organizer_logo_display").setAttribute('bg-image', `storage/organization_logo/${organizerLogo}`);
        squarize();
        bindDivWithImage();
        modal("#modalOrganizer").hide();
    });

    e.preventDefault();
}
const chooseOrganizer = (organizer) => {
    organizer = JSON.parse(escapeJson(organizer));
    let organizerLogo = organizer.logo == "" ? 'default_logo.png' : organizer.logo;
    select("#organizer_name_display").innerText = organizer.name;
    select("#organizer_logo_display").style.backgroundColor = "#fff";
    select("#organizer_logo_display").setAttribute('bg-image', `storage/organization_logo/${organizerLogo}`);
    bindDivWithImage();
    state.field.organizer_id = organizer.id;
    modal("#modalOrganizer").hide();
}
const chooseExecution = (type, button) => {
    state.field.execution_type = type;
    selectAll(".execution_type").forEach(item => item.classList.remove('active'));
    button.classList.add('active');
    select("#executionTypeDescription").innerText = state.execution_type_description[type];
}
const chooseTopic = (topic, button) => {
    if (button.classList.contains('active')) {
        removeArray(topic, state.field.topics);
        button.classList.remove('active');
    } else {
        if (state.field.topics.length < 3) {
            state.field.topics.push(topic);
            button.classList.add('active');
        } else {
            select("#topic_error").classList.remove('d-none');
        }
    }
}
const closeTopicModal = () => {
    let topics = state.field.topics;
    if (topics.length > 0) {
        select("#topic_display").innerText = state.field.topics.join(',');
    } else {
        select("#topic_display").innerText = 'Pilih Topik';
    }
    select("#topic_error").classList.add('d-none');
    modal('#modalTopic').hide();
}

const addTicket = (type) => {
    modal("#modalTicket").show();
    select("#modalTicket #ticket_type").value = type;
    
    if (type == 'gratis') {
        select("#modalTicket #ticketPriceArea").style.display = 'none';
    } else if (type == 'suka-suka') {
        select("#modalTicket #ticketPriceArea").style.display = "none";
        type = "Bayar Sesukamu";
    } else {
        select("#modalTicket #ticketPriceArea").style.display = "block";
    }

    select("#modalTicket #ticket_type_display").innerHTML = type.toUpperCase();
}
const typingCurrency = (input) => {
    let value = input.value;
    let decodedValue = Currency(value).decode();
    state.ticket_price = decodedValue.toString();
    
    input.value = Currency(state.ticket_price).encode();
}
const setTicketQuantity = (action) => {
    if (action == 'increase') {
        if (state.ticket_quantity == 1) {
            state.ticket_quantity = 5;
        } else {
            state.ticket_quantity += 5;
        }
    } else {
        if (state.ticket_quantity - 5 > 0) {
            state.ticket_quantity -= 5;
        }
    }
    select("#modalTicket #ticket_quantity").value = state.ticket_quantity;
}
const renderCreatedTickets = () => {
    select("#renderTicketArea").innerHTML = '';
    state.field.tickets.forEach(ticket => {
        let priceDisplay = "";
        if (ticket.type == "gratis") {
            priceDisplay = "Gratis";
        } else if (ticket.type == "suka-suka") {
            priceDisplay = "Bayar Sesukamu";
        } else {
            priceDisplay = Currency(ticket.price).encode();
        }
        Element("div", {
            class: "TicketDisplay"
        }).render("#renderTicketArea", `<div class="HalfCircle LeftCircle"></div>
        <div class="HalfCircle RightCircle"></div>
        <div class="info">
            <h4 class="m-0">${ticket.name}</h4>
            <div class="text muted small mt-1">${ticket.description}</div>
        </div>
        <div class="flex row item-center mt-2">
            <div class="flex column grow-1">
                <div class="price text small bold primary">${priceDisplay}</div>
                <div class="text small muted mt-05">${moment(ticket.start_date).format('D MMM')} - ${moment(ticket.end_date).format('D MMM YYYY')}</div>
            </div>
            <div class="pointer text primary" onclick="removeTicket('${ticket.key}')"><i class="bx bx-trash"></i></div>
        </div>`);
    })
}
const createTicket = e => {
    let name = select("#modalTicket #ticket_name");
    let description = select("#modalTicket #ticket_description");
    let quantity = select("#modalTicket #ticket_quantity");
    let price = select("#modalTicket #ticket_price");
    let start_date = select("#modalTicket #ticket_start_date");
    let end_date = select("#modalTicket #ticket_end_date");

    let toCreate = {
        key: generateRandomString(12),
        type: select("#modalTicket #ticket_type").value,
        name: name.value,
        description: description.value,
        quantity: quantity.value,
        price: Currency(price.value).decode(),
        start_date: start_date.value,
        end_date: end_date.value,
    };
    state.field.tickets.push(toCreate);

    renderCreatedTickets();
    modal("#modalTicket").hide();

    name.value = '';
    description.value = '';
    quantity.value = 5;
    start_date.value = '';
    end_date.value = '';
    price.value = 'Rp 0';
    state.ticket_quantity = 1;
    state.ticket_price = '0';

    e.preventDefault();
}
const removeTicket = key => {
    state.field.tickets.forEach((ticket, t) => {
        if (ticket.key == key) {
            state.field.tickets.splice(t, 1);
        }
    });
    renderCreatedTickets();
}

const login = (e, payload = null) => {
    if (payload == null) {
        let email = select("#email");
        let password = select("#password");

        payload = {
            email: email.value,
            password: password.value,
        };
    }

    post("/api/user/login", payload)
    .then(res => {
        if (res.status == 200) {
            let user = res.user;
            localStorage.setItem('user_data', JSON.stringify(user));
            state.myData = user;
            if (payload == null) {
                if (user.is_active == 1) {
                    modal("#modalLogin").hide();
                } else {
                    navigateModal("#modalOtp");
                }
            } else {
                modal("#modalLogin").hide();
            }
        } else {
            writeError(res.message);
        }
    });

    if (e !== null) {
        e.preventDefault();
    }
}
const register = (e, payload = null) => {
    if (payload == null) {
        let name = select("#modalRegister #name");
        let email = select("#modalRegister #email");
        let password = select("#modalRegister #password");

        payload = {
            name: name.value,
            email: email.value,
            password: password.value,
        };
    }

    post("api/user/register", payload).then(res => {
        if (res.status == 200) {
            let user = res.user;
            localStorage.setItem('user_data', JSON.stringify(user));
            state.myData = user;

            if (payload == null) {
                if (user.is_active == 0) {
                    navigateModal("#modalOtp");
                    select("#modalOtp #email").innerText = email.value;
                }
            } else {
                modal("#modalRegister").hide();
            }
        } else {
            writeError(res.message);
        }
    });

    if (e !== null) {
        e.preventDefault();
    }
}

const handleCredentialResponse = (response) => {
    let user = parseJwt(response.credential);
    if (state.google_action == 'login') {
        login(null, {
            email: user.email,
            with_google: 1,
        });
    } else {
        register(null, {
            name: user.name,
            email: user.email,
            with_google: 1,
        })
    }
}

const loginWithGoogle = () => {
    google.accounts.id.initialize({
        client_id: select("#gcid").value,
        callback: handleCredentialResponse
    });
    google.accounts.id.prompt(notification => {
        if (notification.isNotDisplayed() || notification.isSkippedMoment()) {
            document.cookie =  `g_state=;path=/;expires=Thu, 01 Jan 1970 00:00:01 GMT`;
            google.accounts.id.prompt()
        }
    });
}

flatpickr("#event_start_date", {
    dateFormat: 'Y-m-d',
    minDate: Date.now(),
    onChange: (selectedDate, dateStr) => {
        select("#event_end_date").value = "";
        flatpickr("#event_end_date", {
            dateFormat: 'Y-m-d',
            minDate: dateStr
        });
    }
});

flatpickr("#ticket_start_date", {
    dateFormat: 'Y-m-d',
    onChange: (selectedDate, dateStr) => {
        select("#ticket_end_date").value = "";
        flatpickr("#ticket_end_date", {
            dateFormat: 'Y-m-d',
            minDate: dateStr
        });
    }
});

flatpickr("#event_start_time", {
    dateFormat: "H:i",
    noCalendar: true,
    enableTime: true,
    time_24hr: true,
    onChange: (selectedDate, dateStr) => {
        select("#event_end_time").value = "";
        flatpickr("#event_end_time", {
            dateFormat: "H:i",
            noCalendar: true,
            enableTime: true,
            time_24hr: true,
        })
    }
});

const saveDateTime = () => {
    let startDate = moment(select("#event_start_date").value);
    let endDate = moment(select("#event_end_date").value);
    let startTime = select("#event_start_time").value;
    let endTime = select("#event_end_time").value;
    select("#time_display").innerHTML = `${startDate.format('D MMMM')} - ${endDate.format('D MMMM Y')}
    <br /><br />
    ${startTime} - ${endTime}`;
    modal('#modalDateTime').hide();
}

if (screen.width < 480) {
    select(".footer button.primary").innerHTML = "<i class='bx bx-chevron-right'></i>";
    select(".footer button.prev").innerHTML = "<i class='bx bx-chevron-left'></i>";
}