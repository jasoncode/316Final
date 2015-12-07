from lxml import html
import requests
import re

def parser():
    file = open('openSecretsOrgData.txt', 'w')
    
    orgInfoData = requests.get("http://www.opensecrets.org/downloads/crp/CRP_Categories.txt")
    orgInfoTree = html.fromstring(orgInfoData.content)
    
    orgInfo = orgInfoTree.xpath('//text()')[0]
    orgInfo = orgInfo[orgInfo.find("Catcode"):].splitlines() #remove unnecessary text and split lines
    orgInfo = [re.split(r'\t+', s) for s in orgInfo][1:] #don't need the first line because it is just column names
    
    for line in orgInfo:
        string = ""
        for s in line:
            string += s + "|"
        file.write(string[:-1]+"\n")

    
    
    file.close()
    
parser()