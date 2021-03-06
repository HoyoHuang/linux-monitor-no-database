# linux-monitor-no-database

## 特色

1. 不需要資料庫
2. 不需要設定定期執行
3. 不需要執行服務
4. 可以得到 CPU 負載、記憶體空間、硬碟空間即時百分比
5. 可以得到即時網路流量以及最近一分鐘流量圖表
6. 手動釋放記憶體快取 CACHE

## 使用方法

1. cd /opt
2. git clone https://github.com/HoyoHuang/linux-monitor-no-database/
3. cd linux-monitor-no-database
4. cd public
5. php -S 0.0.0.0:8888
6. 打開瀏覽器，輸入 ip:port

## 設定

1. 使用 ip a 取得網路卡代號。可能是 eth0 或是 ens18 這樣的代號
2. 使用 lsblk -e7 取得硬碟代號，可能是 sda 或是 nvme0n1 這樣的代號

將以上代號設定 config.php 即可，預設為 eth0 & sda

## 歷史資料

流量的圖表是使用 Javascript 的陣列儲存而來，因此網頁重新整理後就需要重新紀錄

## 成果

![20220623 234315](https://user-images.githubusercontent.com/20652669/175340099-e0cff59c-ac97-44d5-8eed-552d79f6c8bd.png)


# clone 後更新

```shell
# git config --global --add safe.directory /opt/linux-monitor-no-database
# git reset --hard
# git pull
```

然後 `ps -ax` 找到 php 常駐的程式 `kill -9 id` 後就可以重新啟動

