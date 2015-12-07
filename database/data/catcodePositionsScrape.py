from lxml import html
import requests
import re

def parser():
    file = open('catcodePositions.txt', 'w')
    catcodesString = "A0000,A1000,A1100,A1200,A1300,A1400,A1500,A1600,A2000,A2300,A3000,A3100,A3200,A3300,A3500,A4000,A4100,A4200,A4300,A4500,A5000,A5200,A6000,A6500,A8000,B0000,B0500,B1000,B1200,B1500,B2000,B2400,B3000,B3200,B3400,B3600,B4000,B4200,B4300,B4400,B5000,B5100,B5200,B5300,B5400,B5500,B6000,C0000,C1000,C1100,C1300,C1400,C2000,C2100,C2200,C2300,C2400,C2600,C2700,C2800,C2900,C4000,C4100,C4200,C4300,C4400,C4500,C4600,C5000,C5100,C5110,C5120,C5130,C5200,C5300,C5400,C6000,C6100,C6200,C6300,C6400,C6500,D0000,D2000,D3000,D4000,D5000,D6000,D8000,D9000,E0000,E1000,E1100,E1110,E1120,E1140,E1150,E1160,E1170,E1180,E1190,E1200,E1210,E1220,E1230,E1240,E1300,E1320,E1500,E1600,E1610,E1620,E1630,E1700,E2000,E3000,E4000,E4100,E4200,E5000,F0000,F1000,F1100,F1200,F1300,F1400,F1410,F1420,F2000,F2100,F2110,F2200,F2300,F2400,F2500,F2600,F2700,F3000,F3100,F3200,F3300,F3400,F4000,F4100,F4200,F4300,F4400,F4500,F4600,F4700,F5000,F5100,F5200,F5300,F5500,F7000,G0000,G1000,G1100,G1200,G1300,G1310,G1400,G2000,G2100,G2110,G2200,G2300,G2350,G2400,G2500,G2600,G2700,G2800,G2810,G2820,G2840,G2850,G2860,G2900,G2910,G3000,G3500,G4000,G4100,G4200,G4300,G4400,G4500,G4600,G4700,G4800,G4850,G4900,G5000,G5100,G5200,G5210,G5220,G5230,G5240,G5250,G5260,G5270,G5280,G5290,G5300,G5400,G5500,G5600,G5700,G5800,G6000,G6100,G6400,G6500,G6550,G6700,G6800,G7000,H0000,H1000,H1100,H1110,H1120,H1130,H1400,H1500,H1700,H1710,H1750,H2000,H2100,H2200,H2300,H3000,H3100,H3200,H3300,H3400,H3500,H3700,H3800,H3900,H4000,H4100,H4200,H4300,H4400,H4500,H4600,H4700,H5000,H5100,H5150,H5170,H5200,H5300,H6000,J0000,J1000,J1100,J1110,J1200,J1300,J2000,J2100,J2200,J2300,J2400,J2500,J3000,J4000,J5000,J5100,J5200,J5300,J5400,J6100,J6200,J6500,J7000,J7120,J7150,J7200,J7210,J7300,J7400,J7500,J7510,J7600,J7700,J8000,J9000,J9100,JD100,JD200,JE300,JH100,JW100,K0000,K1000,K1100,K1200,K2000,K2100,L0000,L1000,L1100,L1200,L1300,L1400,L1500,L5000,LA100,LB100,LC100,LC150,LD100,LE100,LE200,LG000,LG100,LG200,LG300,LG400,LG500,LH100,LM100,LM150,LT000,LT100,LT300,LT400,LT500,LT600,M0000,M1000,M1100,M1300,M1400,M1500,M1600,M1700,M2000,M2100,M2200,M2250,M2300,M2400,M3000,M3100,M3200,M3300,M3400,M3500,M3600,M4000,M4100,M4200,M4300,M5000,M5100,M5200,M5300,M6000,M7000,M7100,M7200,M7300,M8000,M9000,M9100,M9200,M9300,T0000,T1000,T1100,T1200,T1300,T1400,T1500,T1600,T1700,T2000,T2100,T2200,T2300,T2310,T2400,T2500,T3000,T3100,T3200,T4000,T4100,T4200,T5000,T5100,T5200,T5300,T6000,T6100,T6200,T6250,T7000,T7100,T7200,T8000,T8100,T8200,T8300,T8400,T9000,T9100,T9300,T9400,X0000,X1200,X3000,X3100,X3200,X3300,X3500,X3700,X4000,X4100,X4110,X4200,X5000,X7000,X8000,X9000,Y0000,Y1000,Y2000,Y3000,Y4000,Z1000,Z1100,Z1200,Z1300,Z1400,Z4100,Z4200,Z4300,Z4400,Z4500,Z5000,Z5100,Z5200,Z5300,Z9000,Z9100,Z9500,Z9600,Z9700,Z9800,Z9999"
    catcodes = [catcode for catcode in catcodesString.split(',')]

    for catcode in catcodes:
        catcodePositionsData = requests.get("http://maplight.org/us-congress/interest/"+catcode+"/bills")
        if catcodePositionsData:
            print "data"
            catcodePositionsTree = html.fromstring(catcodePositionsData.content)

            catcodePositions1 = catcodePositionsTree.xpath('//tbody/tr[contains(@class, "odd")]/td[2]/a/text()')
            catcodePositions2 = catcodePositionsTree.xpath('//tbody/tr[contains(@class, "odd")]/td[4]/text()')
            catcodePositions3 = catcodePositionsTree.xpath('//tbody/tr[contains(@class, "odd")]/td[5]/text()')

            catcodePositions1 = catcodePositions1[0::2]

            result = []
            for s in catcodePositions1:
                temp = s.strip().split()
                temp[0] = ''.join([a.lower() for a in temp[0].split('.')])
                temp[2] = temp[2][1:]
                result.append(temp[0]+temp[1]+'-'+temp[2])
            catcodePositions1 = result

            for i in range(len(catcodePositions1)):
                string = catcode +"|"+ catcodePositions1[i] +"|"+ catcodePositions2[i] +"|"+ catcodePositions3[i]
                file.write(string+"\n")
        else:
            print "no data"


    file.close()
    
parser()