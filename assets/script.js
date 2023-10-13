let datas;

const fetchTotal = () => {
    fetch('http://localhost/ForceWan3.0/index.php?action=getAllData')
    .then(response => response.json())
    .then(data => {
        datas = data;
        sortData();
    })
    .catch(error => {
        console.error('Erreur de récupération des données :', error);
    });
}
fetchTotal();


const getParticipantID = (pseudoToFind) => {
    const participant = datas.Participants.find(participant => participant.pseudo === pseudoToFind);
    return participant ? participant.id : null;
}

const calculateTimeDifferenceInSeconds = (start, end) => {
    const startTime = new Date(start).getTime();
    const endTime = new Date(end).getTime(); // Convertion en millisecondes
    return (endTime - startTime) / 1000; // Resultat en secondes
}

const calculateTotalTimeForParticipant = (datesArray) => {
    let totalTimeInSeconds = 0;
    datesArray.forEach(dateRange => {
        const start = dateRange.start;
        const end = dateRange.end;
        if (end == null) {
            totalTimeInSeconds += calculateTimeDifferenceInSeconds(0, 0);
        } else {
            totalTimeInSeconds += calculateTimeDifferenceInSeconds(start, end);
        }

    });
    return totalTimeInSeconds;
}

const countParticipantsAndLikesPerStream = (streamsParticipations, streamsData) => {
    const participantsPerStream = {};
    const streamsLikes = {};

    streamsData.forEach(stream => {
        streamsLikes[stream.id] = parseInt(stream.total_likes, 10);
    });

    streamsParticipations.forEach(participation => {
        const streamId = participation.stream_id;
        const participantId = participation.participant_id;

        if (!participantsPerStream[streamId]) {
            participantsPerStream[streamId] = new Set();
        }

        participantsPerStream[streamId].add(participantId);
    });

    const resultArray = Object.keys(participantsPerStream)
        .sort((a, b) => b - a)
        .slice(0, 8)

        .map(streamId => {
            const uniqueParticipants = participantsPerStream[streamId];
            const count = uniqueParticipants.size;

            return {
                name: `Stream ${streamId}`,
                data: [streamsLikes[streamId] || 0, count],
            };
        });

    return resultArray;
};


function getTotalLikesPerStream(streamsData) {
    return streamsData.map(stream => parseInt(stream.total_likes, 10) || 0);
}

function getTotalParticipantsPerStream(participationsData) {
    const participantsPerStream = {};

    participationsData.forEach(participation => {
        const streamId = participation.stream_id;
        const participantId = participation.participant_id;

        if (!participantsPerStream[streamId]) {
            participantsPerStream[streamId] = new Set();
        }

        participantsPerStream[streamId].add(participantId);
    });

    return Object.keys(participantsPerStream).map(streamId => participantsPerStream[streamId].size);
}


const sortData = () => {
    const pseudos = ['A', 'B', 'C', 'D'];
    let participantsAndLikesCounts = countParticipantsAndLikesPerStream(datas.StreamParticipations, datas.Streams);
    const totalLikesArray = getTotalLikesPerStream(datas.Streams);
    const totalParticipantsArray = getTotalParticipantsPerStream(datas.StreamParticipations);

    if(participantsAndLikesCounts.length === 0){
        participantsAndLikesCounts = [{name: "", data: [0,0]}];
    }

    if (datas.StreamParticipations.length > 0) {
        const participations = datas.StreamParticipations;
        const datesByParticipantId = {};
        participations.forEach(participation => {
            const participantId = participation.participant_id;
            const startParticipateAt = participation.start_participate_at;
            const endParticipateAt = participation.end_participate_at;
            if (!datesByParticipantId[participantId]) {
                datesByParticipantId[participantId] = [];
            }
            datesByParticipantId[participantId].push({ start: startParticipateAt, end: endParticipateAt, streamId: participation.stream_id });
        });

        const streamsById = {};
        participations.forEach(participation => {
            const streamId = participation.stream_id;
            if (!streamsById[streamId]) {
                streamsById[streamId] = [];
            }
            streamsById[streamId].push(participation);
        });

        const allStreamIds = Object.keys(streamsById);
        allStreamIds.sort((a, b) => b - a);
        const lastFourStreamIds = allStreamIds.slice(0, 4);

        const streamsT = lastFourStreamIds.map(streamId => {
            const data = [];
            for (let i = 0; i < 4; i++) {
                const participantId = getParticipantID(pseudos[i]);
                if( datesByParticipantId[participantId] !== undefined ) {
                    const participantData = datesByParticipantId[participantId].filter(entry => entry.streamId === streamId);

                    if (participantData.length > 0) {
                        const totalTime = calculateTotalTimeForParticipant(participantData);
                        data.push(totalTime);
                    } else {
                        data.push(0);
                    }
                } else {
                    data.push(0);
                }
            }
            return {
                name: `stream_id: ${streamId}`,
                data: data,
            };
        });
        displayChart3(streamsT);

    } else {
        const streamT_0 = Array(4).fill({
            name: '?',
            data: [0, 0, 0, 0]
        });
        displayChart3(streamT_0);
    }

    displayChart1(participantsAndLikesCounts);
    displayChart2(totalLikesArray, totalParticipantsArray);
}

const displayChart1 = (participantsAndLikesCounts) => {
    Highcharts.chart('chart1', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Total Participants & Likes in Streams on the 8 last Streams'
        },
        xAxis: {
            categories: ['Likes', 'Participants']
        },
        yAxis: {
            title: {
                text: 'Total Number'
            }
        },
        series: participantsAndLikesCounts
    });
}

const displayChart2 = (totalLikesArray, totalParticipantsArray) => {
    Highcharts.chart('chart2', {

        title: {
            text: 'Participants & Likes Progession',
            align: 'left'
        },

        subtitle: {
            text: '',
            align: 'left'
        },

        yAxis: {
            title: {
                text: 'Total'
            }
        },

        xAxis: {
            accessibility: {
                rangeDescription: 'Range:'
            }
        },

        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'middle'
        },

        plotOptions: {
            series: {
                label: {
                    connectorAllowed: false
                },
                pointStart: 0
            }
        },

        series: [{
            name: 'Participants',
            data: totalParticipantsArray
        }, {
            name: 'Likes',
            data: totalLikesArray
        }],

        responsive: {
            rules: [{
                condition: {
                    maxWidth: 500
                },
                chartOptions: {
                    legend: {
                        layout: 'horizontal',
                        align: 'center',
                        verticalAlign: 'bottom'
                    }
                }
            }]
        }

    });
}

const displayChart3 = (streamsT) => {
    Highcharts.chart('chart3', {
        chart: {
            type: 'bar'
        },
        title: {
            text: 'Time Participation on the 4 last Streams',
            align: 'left'
        },
        subtitle: {
            text: '',
            align: 'left'
        },
        xAxis: {
            categories: ['A', 'B', 'C', 'D'],
            title: {
                text: null
            },
            gridLineWidth: 1,
            lineWidth: 0
        },
        yAxis: {
            min: 0,
            title: {
                text: 'Total Time (in seconds)',
                align: 'high'
            },
            labels: {
                overflow: 'justify'
            },
            gridLineWidth: 0
        },
        tooltip: {
            valueSuffix: 'seconds'
        },
        plotOptions: {
            bar: {
                borderRadius: '50%',
                dataLabels: {
                    enabled: true
                },
                groupPadding: 0.1
            }
        },
        legend: {
            layout: 'vertical',
            align: 'right',
            verticalAlign: 'top',
            x: 10,
            y: 135,
            floating: true,
            borderWidth: 1,
            backgroundColor:
                Highcharts.defaultOptions.legend.backgroundColor || '#FFFFFF',
            shadow: true
        },
        credits: {
            enabled: false
        },
        series: streamsT
    });
}
