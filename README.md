# ZxtonePhone
zxtonephone学生机PHP SDK

        $ys7 = new ZxtoneClient('用户名','密码','key');
        echo "AccessToken=".$ys7->getCookieStore() . "\n";

        echo "获取设备列表\n";
        print_r($ys7->getDeviceList(1,10));

        echo "获取设备信息\n";
        print_r($ys7->getDeviceDetial('8888'));

        echo "修改设备名称\n";
        print_r($ys7->updateDeviceName('8888','芒丁测试'));

        echo "修改设备信息\n";
        print_r($ys7->updateDeviceInfo('A1000006A8039A','芒丁测试','19159001901'));

        echo "获取上课时间段\n";
        print_r($ys7->getClassTime('A1000006A8039A'));

        echo "设置上课时间段\n";
        print_r($ys7->setClassTime('A1000006A8039A','110001600'));

        echo "获取亲情号码\n";
        print_r($ys7->getPhoneConfig('8888'));

        echo "修改设备亲情号码\n";
        print_r($ys7->setPhoneNumber('8888',2,''));
