$(function() {
    let btnEdtNextWeek = $('.nextWeek'),
        btnEdtPrevWeek = $('.prevWeek');

    btnEdtNextWeek.on('click', function() {
        let kine = $(this).data('kine'),
            currentStartDate = document.querySelector('table.table-horizontal.jours[data-kine="' + kine + '"]').getAttribute('data-date'),
            spinner = $('.loading-spinner[data-kine="'+kine+'"]');
        spinner.css('display','flex');

        $.get(
            '/edt/' + kine + '/' + currentStartDate,
            {
                'movement': 'next'
            },
            function(data) {
                let json = JSON.parse(data);
                doCalendar(json)
            },
            'json'
        )
    });
    btnEdtPrevWeek.on('click', function() {
        let kine = $(this).data('kine'),
            currentStartDate = document.querySelector('table.table-horizontal.jours[data-kine="' + kine + '"]').getAttribute('data-date'),
            spinner = $('.loading-spinner[data-kine="'+kine+'"]');
        spinner.css('display','flex');


        $.get(
            '/edt/'+kine+'/'+currentStartDate,
            {
                'movement': 'prev'
            },
            function(data) {
                let json = JSON.parse(data)
                doCalendar(json)
            },
            'json'
        )
    })

    function doCalendar(json) {
        console.log(json)
        let table = $('table.table-horizontal.jours[data-kine=' + json['idKine'] + ']');
        table.empty();
        let tableTxt = '<tbody>'
        $.each(json['dispos'], function(key, val) {
            tableTxt += ('<tr>')
            tableTxt += '<th scope="row">' + val['jour'] + '<br>' + val['date'] + '</th>'
            tableTxt += '<td class="heures">'
            if(Object.keys(val['heures']).length > 0) {
                $.each(val['heures'], function(key, val) {
                    tableTxt += '<a class="btn btn-primary prendre-rdv-btn">' + val + '</a>'
                });
            }
            else {
                tableTxt += '<p class="text-muted text-center">____</p>'
            }
            tableTxt += '</td>'
            tableTxt += '</tr>'
        });
        tableTxt += '</tbody>'
        table.append(tableTxt)

        document.querySelector('table.table-horizontal.jours[data-kine="' + json['idKine'] + '"]').setAttribute('data-date', json['timestamp'])
        $('.loading-spinner[data-kine="'+json['idKine']+'"]').css('display', 'none')
    }
});