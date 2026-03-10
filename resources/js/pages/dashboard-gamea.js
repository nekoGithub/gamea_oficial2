/**
 * Dashboard GAMEA - Gráficos con ApexCharts
 */
import ApexCharts from 'apexcharts';

// Colores institucionales de GAMEA
const coloresGAMEA = {
    rojo: '#D32F2F',      // Backend
    cyan: '#26C6DA',      // Frontend
    amarillo: '#FFB300',  // Otros/Librerías
    borderColor: getComputedStyle(document.documentElement).getPropertyValue('--ct-border-color').trim() || '#e7e7e7',
};

// ========== GRÁFICO 1: AUDITORÍAS POR DÍA (Área) ==========
const auditoriaChartOptions = {
    chart: {
        height: 330,
        type: 'area',
        toolbar: { show: false }
    },
    dataLabels: {
        enabled: false
    },
    stroke: {
        width: 2,
        curve: 'smooth'
    },
    colors: [coloresGAMEA.rojo],
    series: [
        {
            name: 'Auditorías',
            data: totalesAuditorias
        }
    ],
    legend: {
        offsetY: 5,
    },
    xaxis: {
        categories: fechasAuditorias,
        axisBorder: { show: false },
        axisTicks: { show: false },
        labels: {
            style: {
                fontSize: "11px"
            },
            rotate: -45,
        }
    },
    yaxis: {
        labels: {
            formatter: function (val) {
                return Math.floor(val);
            }
        }
    },
    tooltip: {
        shared: true,
        y: {
            formatter: function (val) {
                return val + " actividades";
            }
        }
    },
    fill: {
        type: "gradient",
        gradient: {
            shadeIntensity: 1,
            opacityFrom: 0.4,
            opacityTo: 0.1,
            stops: [15, 120, 100]
        }
    },
    grid: {
        borderColor: coloresGAMEA.borderColor,
        padding: {
            bottom: 0
        }
    }
};

const auditoriaChart = new ApexCharts(
    document.querySelector("#auditoria-chart"),
    auditoriaChartOptions
);
auditoriaChart.render();

// ========== GRÁFICO 2: TECNOLOGÍAS (BURBUJAS POR TIPO) ==========

// Crear series con posiciones aleatorias
const seriesData = tecnologiasData.map((tec, index) => {
    return {
        name: tec.nombre,
        data: [[
            Math.floor(Math.random() * 650) + 75,  // x: entre 75 y 725
            Math.floor(Math.random() * 35) + 15,   // y: entre 15 y 50
            tec.cantidad * 12                       // z: tamaño
        ]]
    };
});

// ✅ ASIGNAR COLORES SEGÚN EL TIPO (no por índice)
const coloresBurbujas = tecnologiasData.map((tec) => {
    if (tec.tipo === 'backend') {
        return coloresGAMEA.rojo;
    } else if (tec.tipo === 'frontend') {
        return coloresGAMEA.cyan;
    } else {
        return coloresGAMEA.amarillo; // otros/librerias
    }
});

const tecnologiasChartOptions = {
    chart: {
        height: 330,
        type: 'bubble',
        toolbar: { show: false }
    },
    dataLabels: {
        enabled: false
    },
    series: seriesData.slice(0, 10), // Top 10 tecnologías
    fill: {
        opacity: 0.8,
        gradient: {
            enabled: false
        }
    },
    colors: coloresBurbujas.slice(0, 10), // ✅ Colores por tipo
    xaxis: {
        tickAmount: 12,
        type: 'numeric',
        min: 0,
        max: 800,
        labels: {
            show: true,
            style: {
                fontSize: "11px"
            }
        }
    },
    yaxis: {
        max: 60,
        labels: {
            style: {
                fontSize: "11px"
            }
        }
    },
    grid: {
        borderColor: coloresGAMEA.borderColor,
        padding: {
            top: -20,
            right: 0,
            bottom: -5,
            left: 10
        }
    },
    legend: {
        show: false
    },
    tooltip: {
        custom: function({series, seriesIndex, dataPointIndex, w}) {
            const tecnologia = tecnologiasData[seriesIndex];
            if (!tecnologia) return '';
            
            const colorBurbuja = coloresBurbujas[seriesIndex];
            
            // Traducir tipo
            let tipoTexto = tecnologia.tipo;
            if (tipoTexto === 'backend') tipoTexto = 'Backend';
            else if (tipoTexto === 'frontend') tipoTexto = 'Frontend';
            else if (tipoTexto === 'otros/librerias') tipoTexto = 'Otros/Librerías';
            
            return `
                <div style="padding: 8px 12px; background: white; border-left: 3px solid ${colorBurbuja};">
                    <div style="font-weight: 600; color: #2D2D2D; margin-bottom: 2px;">
                        ${tecnologia.nombre}
                    </div>
                    <div style="font-size: 12px; color: #757575;">
                        ${tecnologia.cantidad} sistema${tecnologia.cantidad !== 1 ? 's' : ''}
                    </div>
                    <div style="font-size: 11px; color: #9E9E9E; margin-top: 2px;">
                        ${tipoTexto}
                    </div>
                </div>
            `;
        }
    }
};

const tecnologiasChart = new ApexCharts(
    document.querySelector("#tecnologias-chart"),
    tecnologiasChartOptions
);
tecnologiasChart.render();