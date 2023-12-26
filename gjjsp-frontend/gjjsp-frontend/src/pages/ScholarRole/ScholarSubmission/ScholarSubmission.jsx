import React from 'react'
import * as MUI from '../../../import';
import Layout from '../../Components/Layout';

export default function ScholarSubmission() {
  return (
    <Layout>
    <MUI.Grid item xs={12} md={8} lg={9}>
        <MUI.Paper
          sx={{
            p: 2,
            display: 'flex',
            flexDirection: 'column',
            height: 240,
          }}
        >
          <MUI.Typography>
            Scholar Submission
          </MUI.Typography>
        </MUI.Paper>
    </MUI.Grid>
  </Layout>
  )
}
