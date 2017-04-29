#!/bin/bash
# @Author: haodaquan
# @Date:   2017-01-04 17:44:45
# @Last Modified by:   haodaquan
# @Last Modified time: 2017-04-28 09:38:51

#resource必须配置，来源于资源ID
resource='1'
##################
#
# 使用方法：
# 1、修改resource的值，表示本文件将在哪个服务器运行，并改名为 cron_资源ID.sh
# 2、安装执行器。在本服务器中的crontab加上:

# ##<TASK START> FOR PIPI TASK ##
# */1 * * * *  {地址}/uploads/task/cron_资源ID.sh
# ##<TASK END> FOR PIPI TASK ##

##################

cronPath=$(cd $(dirname ${BASH_SOURCE[0]}); pwd )
cronFile="${cronPath}/cron_${resource}"
cronBak="${cronPath}/cronbak_${resource}"
datetime=$(date +%Y%m%d%H%M%S)
newcronFile="${cronPath}""/history/cron_${resource}_""${datetime}"

#查看cron文件是否存在
if [ -f "${cronFile}" ]; then

        crontab -l > "${cronBak}"
        start=`grep -n 'channel start' ${cronBak} | cut -d \: -f 1`
        end=`grep -n 'channel end' ${cronBak} | cut -d \: -f 1`
        
        #linux
		#sed -i ${start},${end}d" "${cronBak}"
		
		#mac
		sed -i ".bak" "${start},${end}d" "${cronBak}"

        new=`cat "${cronFile}"`
        echo "${new}" >> "${cronBak}"

        mv "${cronFile}" "${newcronFile}"
        #cat "${cronBak}"
        crontab "${cronBak}"
fi

#杀死进程
stopFile="${cronPath}/stop_${resource}"
newStopFile="${cronPath}""/history/stop_${resource}_""${datetime}"
if [ -f "${stopFile}" ]; then
        stop=`cat "${stopFile}"`
        for sp in "${stop}" ;do
                arrproc=$(ps -ef | grep "${sp}" | grep -v grep | awk '{print $2}')
                for p in $arrproc; do
                        if [ "${p}"=~^[0-9]+$ ]; then
                                kill -9 "${p}"
                                echo `date "+%Y/%m/%d %H:%M:%S> "` $p " 进程已杀死！"   
                        fi
                done
        done
        mv "${stopFile}" "${newStopFile}"
fi


