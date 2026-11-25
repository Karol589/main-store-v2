/**
 * MAIN-STORE v2.0 - Selects dependientes País > Departamento > Ciudad
 * Requiere en la página: window.BASE_URL y los <select> con id
 * country_id, department_id, city_id. Para editar, los selects de
 * departamento y ciudad traen data-selected con el id a preseleccionar.
 */
document.addEventListener('DOMContentLoaded', () => {
    const selCountry = document.getElementById('country_id');
    const selDep     = document.getElementById('department_id');
    const selCity    = document.getElementById('city_id');

    function reset(select, text) {
        select.innerHTML = `<option value="">${text}</option>`;
        select.disabled = true;
    }

    async function load(select, url, placeholder, selectedId) {
        reset(select, 'Cargando...');
        try {
            const response = await fetch(url);
            const items = await response.json();
            select.innerHTML = `<option value="">${placeholder}</option>`;
            items.forEach(item => {
                // new Option(texto, valor) => no inyecta HTML
                const option = new Option(item.name, item.id);
                option.selected = String(item.id) === String(selectedId);
                select.add(option);
            });
            select.disabled = items.length === 0;
        } catch (err) {
            reset(select, 'Error al cargar');
        }
    }

    selCountry.addEventListener('change', async () => {
        reset(selCity, 'Seleccione primero un departamento');
        if (!selCountry.value) {
            reset(selDep, 'Seleccione primero un país');
            return;
        }
        await load(selDep, `${BASE_URL}api/departments.php?country_id=${selCountry.value}`,
                   'Seleccione un departamento');
    });

    selDep.addEventListener('change', async () => {
        if (!selDep.value) {
            reset(selCity, 'Seleccione primero un departamento');
            return;
        }
        await load(selCity, `${BASE_URL}api/cities.php?department_id=${selDep.value}`,
                   'Seleccione una ciudad');
    });

    // Modo edición: cargar la cadena con los valores guardados
    (async () => {
        if (!selCountry.value) return;
        await load(selDep, `${BASE_URL}api/departments.php?country_id=${selCountry.value}`,
                   'Seleccione un departamento', selDep.dataset.selected);
        if (!selDep.value) return;
        await load(selCity, `${BASE_URL}api/cities.php?department_id=${selDep.value}`,
                   'Seleccione una ciudad', selCity.dataset.selected);
    })();
});
