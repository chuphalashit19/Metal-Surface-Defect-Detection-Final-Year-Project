#!D:/Setup/Anaconda3/python.exe
#/usr/bin/env python
# coding: utf-8

import os
import matplotlib.pyplot as plt
import numpy as np
import cv2
import albumentations as A
import pandas as pd

import torch
import torch.nn as nn
import warnings

from tqdm import tqdm_notebook
from torch.utils.data import DataLoader
from torch.jit import load
from mlcomp.mlcomp.contrib.transform.albumentations import ChannelTranspose
from mlcomp.mlcomp.contrib.dataset.classify import ImageDataset
from mlcomp.mlcomp.contrib.transform.rle import rle2mask, mask2rle
from mlcomp.mlcomp.contrib.transform.tta import TtaWrap
warnings.filterwarnings('ignore')


unet_se_resnext50_32x4d = load('severstal-models/unet_se_resnext50_32x4d.pth')
unet_mobilenet2 = load('severstal-models/unet_mobilenet2.pth')
unet_resnet34 = load('severstal-models/unet_resnet34.pth')


class Model:
    def __init__(self, models):
        self.models = models
    
    def __call__(self, x):
        res = []
        x = x
        with torch.no_grad():
            for m in self.models:
                res.append(m(x))
        res = torch.stack(res)
        return torch.mean(res, dim=0)


model = Model([unet_se_resnext50_32x4d, unet_mobilenet2, unet_resnet34])


def create_transforms(additional):
    res = list(additional)
    # add necessary transformations
    res.extend([
        A.Normalize(
            mean=(0.485, 0.456, 0.406), std=(0.230, 0.225, 0.223)
        ),
        ChannelTranspose()
    ])
    res = A.Compose(res)
    return res


img_folder = 'Dataset/test_images'
batch_size = 2
num_workers = 0

# Different transforms for TTA wrapper
transforms = [
    [],
    [A.HorizontalFlip(p=1)]
]

transforms = [create_transforms(t) for t in transforms]
datasets = [TtaWrap(ImageDataset(img_folder=img_folder, transforms=t), tfms=t) for t in transforms]
loaders = [DataLoader(d, num_workers=num_workers, batch_size=batch_size, shuffle=False) for d in datasets]


thresholds = [0.5, 0.5, 0.5, 0.5]
min_area = [600, 600, 1000, 2000]

res = []
# Iterate over all TTA loaders
total = len(datasets[0])//batch_size
for loaders_batch in tqdm_notebook(zip(*loaders), total=total):
    preds = []
    image_file = []
    for i, batch in enumerate(loaders_batch):
        features = batch['features']
        p = torch.sigmoid(model(features))
        # inverse operations for TTA
        p = datasets[i].inverse(p)
        preds.append(p)
        image_file = batch['image_file']
    
    # TTA mean
    preds = torch.stack(preds)
    preds = torch.mean(preds, dim=0)
    preds = preds.detach().cpu().numpy()
    
    # Batch post processing
    for p, file in zip(preds, image_file):
        file = os.path.basename(file)
        # Image postprocessing
        for i in range(4):
            p_channel = p[i]
            imageid_classid = file+'_'+str(i+1)
            p_channel = (p_channel>thresholds[i]).astype(np.uint8)
            if p_channel.sum() < min_area[i]:
                p_channel = np.zeros(p_channel.shape, dtype=p_channel.dtype)

            res.append({
                'ImageId_ClassId': imageid_classid,
                'EncodedPixels': mask2rle(p_channel)
            })
        
df = pd.DataFrame(res)
df.to_csv('submission.csv', index=False)

df = pd.DataFrame(res)
df = df.fillna('')
df.to_csv('submission.csv', index=False)


df['Image'] = df['ImageId_ClassId'].map(lambda x: x.split('_')[0])
df['Class'] = df['ImageId_ClassId'].map(lambda x: x.split('_')[1])
df['empty'] = df['EncodedPixels'].map(lambda x: not x)
df[df['empty'] == False]['Class'].value_counts()


df = pd.read_csv('submission.csv')[:40]
df['Image'] = df['ImageId_ClassId'].map(lambda x: x.split('_')[0])
df['Class'] = df['ImageId_ClassId'].map(lambda x: x.split('_')[1])

for row in df.itertuples():
    img_path = os.path.join(img_folder, row.Image)
    img = cv2.imread(img_path)
    mask = rle2mask(row.EncodedPixels, (1600, 256))         if isinstance(row.EncodedPixels, str) else np.zeros((256, 1600))
    if mask.sum() == 0:
        continue
    
    fig, axes = plt.subplots(1, 2, figsize=(120, 260))
    axes[0].imshow(img/255)
    axes[1].imshow(mask*60)
    axes[0].set_title(row.Image)
    axes[1].set_title(row.Class)
    file = os.path.splitext(row.Image)[0]
    output_filename = 'Output/' + str(file) + '.jpg'
    plt.savefig(output_filename, dpi=40, bbox_inches='tight')
    # plt.show()

print('Executed!!!!!!!!')
