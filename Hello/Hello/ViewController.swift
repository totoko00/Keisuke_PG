//
//  ViewController.swift
//  Hello
//
//  Created by KEISUKE.Yamaguchi on 2015/12/20.
//  Copyright © 2015年 mycompany. All rights reserved.
//

import UIKit

class ViewController: UIViewController {

    @IBOutlet weak var label: UILabel!
    @IBAction func sayHello() {
        //label.text = "こんにちは"
        if label.text == "ありがとう"{
            label.text = "こんにちは"
        }else{
            label.text = "ありがとう"
        }
            
    }
    override func viewDidLoad() {
        super.viewDidLoad()
        // Do any additional setup after loading the view, typically from a nib.
    }

    override func didReceiveMemoryWarning() {
        super.didReceiveMemoryWarning()
        // Dispose of any resources that can be recreated.
    }


}

